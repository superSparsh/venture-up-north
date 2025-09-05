import { ref, computed, watch } from 'vue'

const STORAGE_KEY = 'vds.myventure.v1'
const ALLOWED_TYPES = new Set(['event', 'experience', 'tour', 'listing', 'town'])

function nowTs() { return Date.now() }

function readLS() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (!raw) return { v: 1, ts: 0, items: [] }
    const parsed = JSON.parse(raw)
    if (Array.isArray(parsed)) return { v: 1, ts: 0, items: parsed } // legacy
    return { v: parsed.v ?? 1, ts: parsed.ts ?? 0, items: Array.isArray(parsed.items) ? parsed.items : [] }
  } catch { return { v: 1, ts: 0, items: [] } }
}

function writeLS(payload) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(payload))
}

function createStore() {
  const state = ref(readLS())           // { v, ts, items }
  const items = computed({
    get() { return state.value.items },
    set(arr) { state.value = { ...state.value, items: arr } }
  })

  // CAS-style persist: only write if our state is not older than LS
  function persist() {
    const current = state.value
    const onDisk = readLS()
    // If LS is newer (ts greater), pull it instead of overwriting
    if (onDisk.ts > current.ts) {
      state.value = onDisk
      return
    }
    // Bump ts and write
    const next = { v: 1, ts: nowTs(), items: items.value }
    state.value = next
    writeLS(next)
  }

  // Sync if someone else (another tab/instance) changes LS
  function onStorage(e) {
    if (e.key !== STORAGE_KEY) return
    const onDisk = readLS()
    if (onDisk.ts > state.value.ts) state.value = onDisk
  }
  window.addEventListener('storage', onStorage)

  watch(items, persist, { deep: true })

  const keyFor = (i) => `${i.type}:${i.id}`
  const keys = computed(() => new Set(items.value.map(keyFor)))

  function validItem(i) {
    return i && i.id != null && ALLOWED_TYPES.has(i.type) && i.title && i.url
  }

  function has(i) { return keys.value.has(keyFor(i)) }

  function add(i) {
    if (!validItem(i)) return
    const k = keyFor(i)
    const idx = items.value.findIndex(x => keyFor(x) === k)
    if (idx === -1) {
      items.value = [...items.value, {
        id: i.id, type: i.type, title: i.title, url: i.url,
        image: i.image ?? null, tags: i.tags ?? null,
        cat_url: i.cat_url ?? null, lat: i.lat ?? null, lng: i.lng ?? null,
        slug: i.slug ?? null, summary: i.summary ?? null
      }]
    } else {
      const next = [...items.value]
      next[idx] = { ...next[idx], ...i }
      items.value = next
    }
  }

  function remove(i) {
    const k = keyFor(i)
    items.value = items.value.filter(x => keyFor(x) !== k)
  }

  function toggle(i) { has(i) ? remove(i) : add(i) }

  function reorder(newOrder) {
    if (Array.isArray(newOrder)) items.value = newOrder
  }

  function clear() {
    // set empty array with a NEW timestamp so stale instances cannot overwrite it
    state.value = { v: 1, ts: nowTs(), items: [] }
    writeLS(state.value)
  }

  function exportPayload() {
    return btoa(unescape(encodeURIComponent(JSON.stringify(items.value))))
  }
  function importPayload(b64) {
    try {
      const arr = JSON.parse(decodeURIComponent(escape(atob(b64))))
      items.value = Array.isArray(arr) ? arr : []
    } catch { }
  }

  // Small dev log to confirm single instance
  if (import.meta.env.DEV) console.log('[useMyVenture] singleton ready, items:', items.value.length)

  return { items, has, add, remove, toggle, reorder, clear, exportPayload, importPayload }
}

/* ---------- HARD SINGLETON ---------- */
let store = globalThis.__vdsVentureStore || null
if (!store) {
  store = createStore()
  globalThis.__vdsVentureStore = store
}
if (import.meta && import.meta.hot) {
  import.meta.hot.accept()
  import.meta.hot.dispose(() => { globalThis.__vdsVentureStore = store })
}

export function useMyVenture() { return store }
