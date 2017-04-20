<template>
  <div class="inner">
      <div class="channel-info" :style="{'border-bottom-color': channel.color }">

          <div class="channel-img" :style="{
              'background-color': channel.color,
              'background-image': (channel.channel.img) ? 'url(' + channel.channel.img + ')' : 'none'
              }">
              <h1 class="text-thumb" v-if="!channel.channel.img">{{ channel.channel.title[0] }}</h1>
          </div>
          <!-- end channel logo -->

          <h3>
              <span v-if="!editing">{{ channel.channel.title }}</span>

              <span v-if="editing">
                  <input class="edit-title-input" v-if="editing" placeholder="Rename channel title" v-model="editing"></input>
                  <input type="color" v-model="channel.color" v-if="editing">

                  <el-button @click.prevent="renameChannel()" type="success" size="small" icon="circle-check">Save</el-button>
                  <el-button @click.prevent="editing = null"  size="small" type="text">Cancel</el-button>
              </span>

              <span v-if="!editing">
                  <el-button @click="editing = channel.channel.title" size="mini" icon="edit"></el-button>
                  <el-button @click="deleteChannel(channel.url)" size="mini" type="danger" icon="delete"></el-button>
              </span>
          </h3>
          <!-- end editable channel title and color -->

          <p>
              {{ channel.channel.description }}
              - <a :href="channel.channel.link" target="_blank">{{ channel.channel.link }}</a>
          </p>

          <el-row>
            <el-col :sm="18" :xs="16">
              <p class="channel-meta">
                  <el-badge class="fav-count" :value="channel.items.length" /> posts found

                  <i class="el-icon-date"></i> Blog updated <span class="text-dark">{{ updatedTime(channel.channel.lastBuildDate) }}</span>
                  <i class="el-icon-time"></i> Last synced <span class="text-dark">{{ updatedTime(channel.updated) }}</span>

                  <transition v-if="loading" name="fade">
                      <span class="loading-feed">
                          <i class="el-icon-loading"></i> checking feed...
                      </span>
                  </transition>
                  <!-- end checking feed -->
              </p>
            </el-col>
          </el-row>
          <!-- end channel meta -->
      </div>
      <!-- end channel info -->

      <feed :items="channel.items" :query="$parent.query"></feed>
  </div>
</template>

<script>
  import Feed from './Feed.vue';
  import { common } from '../utils';

  export default {
    props: ['index', 'title'],
    mounted() {
        this.fetchFeed();
    },
    data() {
        return {
            loading: false,
            editing: null
        }
    },
    components: { Feed },
    computed: {
        channel () {
            return this.$store.getters.channels[this.index];
        }
    },
    methods: {
        updatedTime(time) {
            return common.timeAgo(time);
        },
        fetchFeed() {
            this.loading = true;
            this.$store.dispatch('addChannel', { channel:  this.channel }).then((res) => {
                this.loading = false;
            }).catch((error) => {
                this.loading = false;
                let errorMsg = error.response ? error.response.data.msg : error;
                this.$message.error('Oops, ' + errorMsg);
            });
        },
        deleteChannel(url) {
            this.$confirm( this.channel.channel.title + ' channel will permanently deleted?', 'Confirm Delete', {
              confirmButtonText: 'Delete it',
              cancelButtonText: 'Cancel',
              type: 'warning',
              confirmButtonClass: 'el-button--danger'
            }).then(() => {
                this.$message.success( this.channel.channel.title + ' channel has been deleted.');
                this.$store.commit('DELETE_CHANNEL', url);
            }).catch(() => console.info('Delete cancel'))
        },
        renameChannel() {
            if( this.editing.trim() == '' ) {
                this.$message.error('Enter a title for channel');
                return;
            }

            this.$store.dispatch('renameChannel', {
                title: this.editing,
                color: this.channel.color,
                url: this.channel.url
            });

            this.channel.channel.title = this.editing;
            this.editing = null;
        }
    },
    watch: {
        '$route' (to) {
          this.fetchFeed();
        }
    }
}
</script>
