<template>
  <div>
    <form @submit.prevent="postData">
        <my-form @input="setForm" :error="error"/>
        <button>sousinn
        </button>
    </form>
  </div>
</template>

<script>
import MyForm from '../components/MyForm'
import Axios from 'axios';
export default {
  components: {
    MyForm
  },

  data () {
    return {
      formData: {},
      error: {}
    }
  },

  methods: {
    setForm (data) {
      Object.assign(this.formData, data)
    },

    async postData () {
      const newFormData = new FormData()

      Object.keys(this.formData).forEach((key) => {
        newFormData.append(key, this.formData[key])
      })
      // newFormData.append('name', this.formData.name)
      // newFormData.append('myfile', this.formData.myfile)

      // これは使えない
      //Object.assign(newFormData, this.formData)

      console.log(newFormData);

      const response = await Axios.post('/api/image/confirm', newFormData).catch(err => err.response | err)
      console.log(response);
      if (response.status !== 200) {
        this.error = response.data.errors
        return
      }

      this.$store.commit('image/setState', this.formData)
    }
  }
}
</script>

