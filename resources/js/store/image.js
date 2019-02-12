const state = {
  'name': null,
  'myfile': null
}

const mutations = {
  setState(state, data) {
    Object.assign(state, data)
  }
}

export default {
  namespaced: true,
  state,
  mutations
}
