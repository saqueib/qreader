<template>
  <div :class="{ 'full-height': favs.length === 0 }" class="inner">

    <div class="jumbotron" v-if="!favs.length">
        <h2>Your ♥ Favorites</h2>
        <p>You dont have any favorite posts.</p>
    </div>
    <!-- end jumbotron for empty favs -->

    <div v-if="favs.length" class="channel-info">
        <div class="channel-img">
            <h1 class="text-thumb">
                <i class="el-icon-star-on"></i>
            </h1>
        </div>

        <h3>Favorites</h3>
        <p>All the posts you liked and choose to keep are here.</p>
        <p class="channel-meta">
            <span class="text-dark">You have <el-badge class="fav-count" :value="favs.length" /> </span> post(s)
        </p>
    </div>
    <!-- end header for favs -->

    <feed :items="favs" :fav="true" :query="$parent.query"></feed>
  </div>
</template>

<script>
  import Feed from './Feed.vue';

  export default {
      computed : {
          favs() {
            return this.$store.getters.favs;
          }
      },
      components: { Feed }
  }
</script>
