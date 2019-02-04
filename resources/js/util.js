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

export const OK = 200
export const CREATED = 201
export const INTERNAL_SERVER_ERROR = 500

