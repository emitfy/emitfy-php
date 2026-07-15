/**
 * Decide se emitfy/sdk (PHP) deve ganhar
 nova tag/release (Packagist).
 *
 * exit 0  
→ publicar (criar tag)
 * exit 10 → pular
 (SDK igual ao latest no Packagist)
 * exit 1
  → erro (mudou mas a versão em VERSION j�
� foi tagueada)
 *
 * Env:
 *   SDK_VERSION �
�� opcional; senão lê ../VERSION
 */
import
 { createHash } from 'node:crypto'
import {
 
 existsSync,
  mkdtempSync,
  mkdirSync,
  re
adFileSync,
  readdirSync,
  rmSync,
  statSy
nc,
  writeFileSync
} from 'node:fs'
import {
 tmpdir } from 'node:os'
import { dirname, jo
in, relative } from 'node:path'
import { file
URLToPath } from 'node:url'
import { execSync
 } from 'node:child_process'

const root = jo
in(dirname(fileURLToPath(import.meta.url)), '
..')
const packageName = 'emitfy/sdk'
const u
serAgent = 'EmitfySDKPublish (mailto=dev@emit
fy.com)'

function walkFiles(dir, files = [])
 {
  for (const name of readdirSync(dir)) {
 
   const path = join(dir, name)

    if (stat
Sync(path).isDirectory()) {
      walkFiles(p
ath, files)
      continue
    }

    files.p
ush(path)
  }

  return files
}

/**
 * @para
m {unknown} value
 * @returns {unknown}
 */
f
unction sortKeys(value) {
  if (Array.isArray
(value)) {
    return value.map(sortKeys)
  }


  if (value && typeof value === 'object') {

    const object = /** @type {Record<string,
 unknown>} */ (value)
    const sorted = {}


    for (const key of Object.keys(object).sor
t()) {
      sorted[key] = sortKeys(object[ke
y])
    }

    return sorted
  }

  return va
lue
}

/**
 * Hash estável: src/ + composer.
json (Packagist não usa "version" no compose
r.json).
 * @param {string} base
 */
function
 contentHash(base) {
  const hash = createHas
h('sha256')
  const composerPath = join(base,
 'composer.json')

  if (existsSync(composerP
ath)) {
    const parsed = JSON.parse(readFil
eSync(composerPath, 'utf8'))
    delete parse
d.version
    hash.update('composer.json\0')

    hash.update(JSON.stringify(sortKeys(parse
d)))
    hash.update('\0')
  }

  const srcDi
r = join(base, 'src')

  if (!existsSync(srcD
ir)) {
    throw new Error(`src/ missing in $
{base}`)
  }

  const files = walkFiles(srcDi
r).sort((a, b) =>
    relative(base, a).local
eCompare(relative(base, b))
  )

  for (const
 file of files) {
    const rel = relative(ba
se, file).replaceAll('\\', '/')
    const bod
y = readFileSync(file, 'utf8').replaceAll('\r
\n', '\n')
    hash.update(rel)
    hash.upda
te('\0')
    hash.update(body)
    hash.updat
e('\0')
  }

  return hash.digest('hex')
}

f
unction readVersion() {
  if (process.env.SDK
_VERSION) {
    return process.env.SDK_VERSIO
N.replace(/^v/, '')
  }

  const versionPath 
= join(root, 'VERSION')

  if (!existsSync(ve
rsionPath)) {
    throw new Error('VERSION fi
le missing (and SDK_VERSION unset)')
  }

  r
eturn readFileSync(versionPath, 'utf8').trim(
).replace(/^v/, '')
}

/**
 * @returns {Promi
se<{ version: string, distUrl: string } | nul
l>}
 */
async function fetchPackagistLatest()
 {
  const response = await fetch(`https://re
po.packagist.org/p2/${packageName}.json`, {
 
   headers: { 'User-Agent': userAgent }
  })


  if (response.status === 404) {
    return 
null
  }

  if (!response.ok) {
    throw new
 Error(`Packagist HTTP ${response.status}`)
 
 }

  const data = await response.json()
  co
nst versions = data?.packages?.[packageName]


  if (!Array.isArray(versions) || versions.l
ength === 0) {
    return null
  }

  const l
atest = versions[0]
  const distUrl = latest?
.dist?.url

  if (!distUrl) {
    throw new E
rror('Packagist latest has no dist.url')
  }


  return {
    version: String(latest.versio
n).replace(/^v/, ''),
    distUrl
  }
}

/**

 * @param {string} url
 * @param {string} des
tDir
 */
async function downloadAndExtractZip
(url, destDir) {
  const response = await fet
ch(url, {
    headers: { 'User-Agent': userAg
ent, Accept: 'application/vnd.github+json' },

    redirect: 'follow'
  })

  if (!response
.ok) {
    throw new Error(`download HTTP ${r
esponse.status}: ${url}`)
  }

  const buffer
 = Buffer.from(await response.arrayBuffer())

  const zipPath = join(destDir, 'pkg.zip')
  
writeFileSync(zipPath, buffer)

  // GitHub z
ipballs → uma pasta raiz (tar do Windows/Li
nux abre .zip)
  execSync(`tar -xf "${zipPath
}" -C "${destDir}"`, { stdio: 'pipe' })

  co
nst entries = readdirSync(destDir).filter((na
me) => name !== 'pkg.zip')
  const folder = e
ntries.find((name) => statSync(join(destDir, 
name)).isDirectory())

  if (!folder) {
    t
hrow new Error('zip extract: no root folder')

  }

  return join(destDir, folder)
}

const
 version = readVersion()
const localHash = co
ntentHash(root)
const remote = await fetchPac
kagistLatest()

if (!remote) {
  console.log(
`no remote package — publish ${packageName}
@${version}`)
  process.exit(0)
}

const work
 = mkdtempSync(join(tmpdir(), 'emitfy-php-cmp
-'))

try {
  mkdirSync(work, { recursive: tr
ue })
  const remoteRoot = await downloadAndE
xtractZip(remote.distUrl, work)
  const remot
eHash = contentHash(remoteRoot)

  if (localH
ash === remoteHash) {
    console.log(
      
`SDK unchanged vs ${packageName}@${remote.ver
sion} — skip publish (${localHash.slice(0, 
12)})`
    )
    process.exit(10)
  }

  if (
remote.version === version) {
    console.err
or(
      `SDK source changed, but ${packageN
ame}@${version} already exists on Packagist. 
Bump sdks/php/VERSION.`
    )
    process.exi
t(1)
  }

  console.log(
    `SDK changed (${
localHash.slice(0, 8)} ≠ ${remoteHash.slice
(0, 8)}) — publish v${version}`
  )
  proce
ss.exit(0)
} finally {
  rmSync(work, { recur
sive: true, force: true })
}


