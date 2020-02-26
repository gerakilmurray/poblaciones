<template>
  <div class="logoDiv">
    <img class="logoIcon" src="/static/img/rcr.png" />
  </div>
</template>

<script>
export default {
  name: "logoFloat"
};
</script>

<style scoped>
.logoDiv {
  float: right;
  position: fixed;
  z-index: 1;
  opacity: 75%;
  width: calc(75% - 55px);
  height: 8%;
  overflow: hidden;
  bottom: 20px;
  left: 0;
  text-align: right;
}
.logoIcon{
  width: auto;
  height: 100%;
}
</style>

