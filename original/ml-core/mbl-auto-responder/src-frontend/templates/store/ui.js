import { writable } from 'svelte/store'

export const ui = writable({
  timerId: null,
  loading: false
})

export const error = writable('')
