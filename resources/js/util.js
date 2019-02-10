export function getCookieValue (searchKey) {
  if(typeof searchKey === 'undefined') {
    return ''
  }

  document.cookie.split(';').forEach(cookie => {
    const [key, value] = cookie.split('=')
    if (key === searchKey) {
      return value
    }
  })

  return ''
}

export function isStatusOk (response) {
  if (response.status !== OK) {
    this.$store.commit('error/setCode', response.status)
    return false
  }
  return true
}

export const OK = 200
export const CREATED = 201
export const INTERNAL_SERVER_ERROR = 500
export const UNPROCESSABLE_ENTITY = 422
export const UNAUTHORIZED = 419
export const NOT_FOUND = 404
