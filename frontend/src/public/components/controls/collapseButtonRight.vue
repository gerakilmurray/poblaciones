<template>
	<div v-on:click="doToggle" class='fa fa-2x hand right-arrow'
		  :class="{ 'fa-caret-right': !collapsed, 'fa-caret-left': collapsed }"
		  :style="{ right: right + 'px' }">
		<div class="border-right"></div>
	</div>
</template>

<script>
export default {
	name: 'collapseButtonRight',
	props: [
		'collapsed',
		'startRight',
	],
	data() {
		return {
			right: 0,
		};
	},
	mounted() {
		this.right = 0;
	},
	computed: {
		onLoc() {
			return this.startRight;
		},
		offLoc() {
			return 0;
		},
	},
	methods: {
		doToggle(e) {
			e.preventDefault();
			this.$emit('click', e);
		},
	},
	watch: {
		collapsed() {
			if(this.collapsed) {
				this.right = this.offLoc;
			} else {
				this.right = this.onLoc;
			}
		},
	},
};
</script>

<style scoped>
.right-arrow {
	position:absolute;
	height: 48px;
	width: 23px;
	margin-left: 1px;
	top: 50px;
	font-size: 14px;
	padding-top: 17px;
  padding-right: 2px;
  color: #666;
	background: rgba(255,255,255,0.9);
  box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.3);
	z-index: 1;
}
.border-left {
	position: absolute;
	height: 48px;
	width: 1px;
	top: 0;
	background-color: #D4D4D4;
	left: -1px;
}
</style>

