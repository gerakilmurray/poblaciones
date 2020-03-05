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
  height: 8%;
  overflow: hidden;
  top: 89.6%;
  right: 46px;
	min-width: 200px;
	z-index: 1;
  position:absolute;
  background:seashell;
  border-radius: 21px;
  padding: 5px;
}
.logoIcon{
  width: auto;
  height: 100%;
}
</style>

