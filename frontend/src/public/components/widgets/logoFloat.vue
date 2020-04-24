<template>
  <div v-if="work.Current.Logo" class="logoDiv">
    <img class="logoIcon" :src="work.Current.Logo" />
  </div>
</template>

<script>
export default {
  name: "logoFloat",
  props: [
		'work',
	]
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

