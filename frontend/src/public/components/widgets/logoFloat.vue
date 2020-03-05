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
  opacity: 75%;
  height: 5.5rem;
  overflow: hidden;
  bottom: 2.5rem;
  right: 46px;
  min-height: 40px;
  z-index: 1;
  position: absolute;
  background: seashell;
  border-radius: 21px;
  padding: 0.25em;
}
.logoIcon {
  width: auto;
  height: 100%;
}
</style>

