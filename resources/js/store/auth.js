import Axios from "axios"
import { OK, UNPROCESSABLE_ENTITY} from '../util'

const state = {
  user: null,
  apiStatus: null,
  loginErrorMessages: null,
  registerErrorMessages: null
}

const getters = {
  check: state => !!state.user,
  username: state => state.user ? state.user.name : ''
}

const mutations = {
  setUser (state, user) {
    state.user = user
  },

  setApiStatus (state, apiStatus) {
    state.apiStatus = apiStatus
  },

  setLoginErrorMessages (state, messages) {
    state.loginErrorMessages = messages
  },

  setRegisterErrorMessages (state, messages) {
    state.registerErrorMessages = messages
  }
};


const postUserData = async (context, data, operation) => {
  context.commit('setApiStatus', null)
  const response = await Axios.post(`/api/${operation}`, data)

  if (response.status === OK) {
    context.commit('setApiStatus', true)
    context.commit('setUser', response.data)
    return false
  }

  context.commit('setApiStatus', false)
  if (response.status === UNPROCESSABLE_ENTITY) {
    const operationUpperCase = operation.charAt(0).toUpperCase() + operation.slice(1)
    context.commit(`set${operationUpperCase}ErrorMessages`, response.data.errors)
  } else {
    context.commit('error/setCode', response.status, {root: true})
  }
}

const actions = {
  async register (context, data) {
    return postUserData(context, data, 'register')
  },

  async login (context, data) {
    return postUserData(context, data, 'login')
  },

  async logout (context) {
    await Axios.post('/api/logout')
    context.commit('setUser', null)
  },

  async currentUser (context) {
    const response = await Axios.get('/api/user')
    const user = response.data || null
    context.commit('setUser', user)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
