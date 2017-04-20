<template>
  <aside class="sidebar" :class="{ collapse: collapse }">

      <h1 class="brand">
           <a href="/">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 486.4 486.4"><path d="M0.2 0v93.3c216.5 0 392.7 176.4 392.7 393.1h93.5C486.4 218.3 268.2 0 0.2 0z" class="a"/><path d="M0.1 165.2v93.2c60.7 0 117.8 23.7 160.8 66.8 42.9 42.9 66.6 100.2 66.6 161.2h93.7C321.1 309.3 177 165.2 0.1 165.2z" class="a"/><path d="M64.8 356.6C29 356.6 0 385.7 0 421.2c0 35.7 29 64.5 64.8 64.5 35.8 0 64.8-28.8 64.8-64.5C129.6 385.8 100.6 356.6 64.8 356.6z" class="a"/></svg>
            <span class="brand-name">
                <span class="brand-primary">Q</span>Reader
            </span>
           </a>
      </h1>
      <!-- end brand -->

      <el-menu :router="true" mode="vertical" theme="dark" :default-active="currentIndex">
          <el-menu-item index="/">
              <el-tooltip class="item" effect="dark" content="Dashboard" placement="right">
                <i class="el-icon-menu"></i>
              </el-tooltip>
            <span>Dashboard</span>
          </el-menu-item>

          <el-menu-item-group title="Favorites">
              <el-menu-item index="favorites" :route="{ name: 'Favs'}">
                <el-tooltip class="item" effect="dark" content="Favorites" placement="right">
                  <i class="el-icon-star-on" style="color: red"></i>
                </el-tooltip>
                <span>Favorites </span> <el-badge class="fav-count" :value="favCount" />
              </el-menu-item>
          </el-menu-item-group>

          <el-menu-item-group title="Channels">
            <li class="el-menu-item" index="newChannel" @click="toggleChannelDialog()">
              <el-tooltip class="item" effect="dark" content="Add Channel" placement="right">
                <i :style="{ color: '#13ce66', opacity: 1 }" class="el-icon-plus"></i>
              </el-tooltip>
              <span>Add Channel</span>
            </li>

            <el-menu-item exact v-for="(item, index) in channels"
              :index="index.toString()"
              :key="index"
              :route="{ name: 'Channel', params: { title: slug(item.channel.title), index: index }}">
                <el-tooltip class="item" effect="dark" :content="item.channel.title" placement="right">
                 <img :src="getFavicon(item.channel.link)" alt="">
                </el-tooltip> <span>{{ item.channel.title }}</span>
            </el-menu-item>
          </el-menu-item-group>
      </el-menu>
      <!-- end side menu -->

      <p class="copyright">
        <a  class="collapse-btn toggle" @click.prevent="collaseSideBar" href="#">
          <i class="el-icon-caret-left"></i>
        </a>
        <span class="by">by <a target="_blank" href="http://www.qcode.in">QCode.in</a> <small>v1.0</small></span>
      </p>
      <!-- end copyright -->

  </aside>
  <!-- end sidebar -->
</template>
<script>
  import { common } from '../utils';

  export default {
    methods: {
      toggleChannelDialog() {
        this.$store.commit('TOGGLE_ADD_CAHNNEL');
      },
      slug(str) {
        return common.slug(str);
      },
      getFavicon(url) {
        return 'https://www.google.com/s2/favicons?domain_url=' + url
      },
      collaseSideBar() {
        this.$store.commit('TOGGLE_SIDEBAR')
      }
    },
    computed: {
      channels () {
        return this.$store.getters.channels;
      },
      favCount() {
        return this.$store.getters.favCount;
      },
      currentIndex() {
        if( this.$route.name === 'Favs' ) return 'favorites';
        return this.$route.params.hasOwnProperty('index') ? this.$route.params.index.toString() : '/';
      },
      collapse() {
        return this.$store.getters.collapse;
      }
    }
  }
</script>
