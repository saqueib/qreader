<template>
  <div id="app" class="wrapper">
    <section class="main">
        <!-- sidebar -->
        <sidebar></sidebar>

        <div class="content">
            <header :class="{ collapse: collapse }">
              <el-row>
                   <el-col :span="24">
                    <el-autocomplete
                      class="block-input"
                      v-model="query"
                      :icon="query ? 'close' : 'search'"
                      :on-icon-click="clearSearchInput"
                      :fetch-suggestions="querySearch"
                      placeholder="Search..."
                      :trigger-on-focus="false"
                      @select="handleSelect"
                    ></el-autocomplete>
                   </el-col>
              </el-row>
            </header>
            <!-- end header -->

            <transition name="content">
              <router-view></router-view>
            </transition>
            <!-- main router view outlet -->
        </div>
        <!-- end content -->
    </section>
    <!-- end main section -->

    <el-dialog title="Add a Channel" v-model="showChannelDialog">

      <el-form :model="form" :rules="rules" ref="channelForm">
        <el-form-item label="Channel Feed Url" prop="url">
          <el-input :required="true" type="url" placeholder="ex. http://www.wordpress.com/feed" v-model="form.url" auto-complete="on"></el-input>
        </el-form-item>
        <el-form-item label="Channel color" prop="color">
          <el-input type="color" placeholder="Pick a color for channel" v-model="form.color"></el-input>
        </el-form-item>
      </el-form>
      <!-- end add channelForm -->

      <span slot="footer" class="dialog-footer">
        <el-button @click="toggleChannelDialog()">Cancel</el-button>
        <el-button :loading="loading" type="primary" @click.prevent="submit('channelForm')">{{ loading ? 'Adding' : 'Add' }}</el-button>
      </span>

    </el-dialog>
    <!-- end add channel dialog -->

  </div>
  <!-- end wrapper -->
</template>

<script>
  import Sidebar from './components/Sidebar.vue';
  import { common } from './utils';

  export default {
    name: 'app',
    computed: {
      showChannelDialog() {
        return this.$store.getters.showAddChannel;
      },
      channels() {
        return this.$store.getters.channels
      },
      collapse() {
        return this.$store.getters.collapse;
      }
    },
    data() {
      return {
        loading: false,
        form: {
          color: common.randomColor()
        },
        rules: {
          url: [{
            required: true,
            type: 'url',
            message: 'Please enter a valid feed url', trigger: 'blur'
          }]
        },
        query: ''
      }
    },
    components: { Sidebar },
    methods: {
      toggleChannelDialog() {
         this.$store.commit('TOGGLE_ADD_CAHNNEL');
      },
      submit(form) {
        this.$refs[form].validate((valid) => {
          if (!valid) return false;

          this.loading = true;
          this.$store.dispatch('addChannel', { channel:  this.form }).then(res => {

              // hide the add channel box and show message
              this.toggleChannelDialog();
              this.$message({
                message: 'Congrats! ' + res.channel.title + ' channel has been added.',
                type: 'success'
              });

              // reset form
              this.form.url = '';
              this.loading = false;
              this.form.color = common.randomColor();

              // navigate to new channel
              this.$router.push({ name: 'Channel', params: {
                index: 0, title: res.channel.title.toLowerCase().replace(' ', '-')}
              });

            }).catch( (error) => {
                this.loading = false;
                let errorMsg = error.response ? error.response.data.msg : error;
                this.$message.error('Oops, ' + errorMsg);
            });
        });
      },
      clearSearchInput() {
        this.query = '';
      },
      querySearch(queryString, cb) {
          // get all channels
          var feeds = this.channels;

          // result holder
          let results = [];

          if( queryString ) {
            // search in all channels post titles
            feeds.forEach( (channel, channelIndex) => {
              let matched = channel.items.forEach( (post) => {
                  if(post.title.toLowerCase().indexOf(queryString.toLowerCase()) !== -1) {
                    results.push( {
                      value : post.title,
                      link: post.link,
                      channel: channel.channel.title,
                      channelIndex: channelIndex
                    });
                  }
              })
            })

            // call callback function to return suggestions
            cb(results);
          }
        },
        handleSelect(item) {
          // navigate to chosen result
          this.$router.push({ name: 'Channel', params: {
            index: item.channelIndex,
            title: common.slug(item.channel)
          }});
        }
    }
  }
</script>

<style lang="scss">
@import './assets/scss/app';
</style>
