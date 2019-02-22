<template>
  <div>
    <div>
      <input type="text" name="name" @change="setData({'name': $event.target.value})">
      <span v-if="error.name">{{ error.name }}</span>
      <span v-if="name">{{ name }}</span>
    </div>

    <div>
      <input type="file" name="myfile" @change="onFileChange"/>
      <span v-if="error.myfile"> {{ error.myfile }}</span>
      <span v-if="myfile">{{ myfilename }}</span>
    </div>
  </div>
</template>

<script>
import {mapState} from 'vuex'
export default {
  props: {
    error: {
      type: Object
    }
  },

  computed: {
    myfilename () {
      return this.myfile.name
    },

    // imageKeys () {
    //   return Object.keys(this.$store.state.image)
    // },
    ...mapState('image', ['name', 'myfile']),
  },

  methods: {
    setData (data) {
      console.log(data);

      this.$emit('input', data)
    },

    onFileChange(event) {
      if (event.target.files.length === 0) {
        return
      }

      if (!event.target.files[0].type.match('image.*')) {
        return
      }

      this.setData({'myfile': event.target.files[0]})
    }
  }
}
</script>
