<template>
	<div>
    <!--  The Modal -->
    <boardal v-if="modal.isOpen" :has-mask="modal.hasMask" :can-click-mask="modal.canClickMask" :has-x="modal.hasX" @toggle="toggleModal">
      <article v-cloak>
        <section>
          <div class="articleTitle">
            <div class="closeButton" v-on:click="toggleModal">
              <close-icon title="Cerrar" />
            </div>
            Embeber este mapa
           </div>
          <div class="articleContent">
            <div style="margin-left: 60px;">
              <div style="margin-right: 60px;">
              Para embeber este mapa copiar el siguiente HTML en su sitio web y pegarlo en el código fuente de su página.
              </div>
              </div>
            </div>
        </section>

      </article>
      <footer>
        <div class="forward-actions">
          <!--         <button class="secondary skip" :disabled="isLastStep" v-show="!isLastStep" @click="skip(2)">Skip</button> -->
          <button class="primary next" :disabled="isLastStep" v-show="!isLastStep" @click="skip(1)">
            <i class="fa fa-fw fa-lg" :class="nextIcon"></i>
          </button>
          <button class="accent save" :disabled="!isLastStep" v-show="isLastStep" @click="finish">
            <i class="fa fa-fw fa-lg fa-check"></i>
          </button>
        </div>
        <div class="step-dots" v-if="hasDots">
          <div class="step-dot" v-for="n in max" :key="n" :class="{active: n == step}" @click="goToStep(n)"></div>
        </div>
        <div class="back-actions">
          <button class="secondary cancel prev" :disabled="isFirstStep" xv-show="!isFirstStep" @click="skip(-1)">
            <i class="fa fa-fw fa-lg" :class="backIcon"></i>
          </button>
        </div>
      </footer>
    </boardal>
  </div>
</template>

<script>
import boardal from '@/public/components/widgets/boardal';
import CloseIcon from 'vue-material-design-icons/Close.vue';

export default {
	name: 'embedded',
  components: { boardal, CloseIcon },
  data() {
    return {
      modal: {
        isOpen: false,
        hasMask: true,
        canClickMask: true,
        hasX: false
      },

      showDots: true,
      orientation: 'row',
      // xray: 'hidden'
    };
  },
  computed: {
    isFirstStep(){
      return (this.step === 1);
    },
    isLastStep(){
      return (this.step === this.max);
    },
    hasDots(){
      return (this.max > 1 && this.showDots);
    },
    x_multiplier(){
      return (this.orientation === 'row' ? -1 : 0);
    },
    y_multiplier(){
      return (this.orientation === 'row' ? 0 : -1);
    },
    axis() {
      return (this.orientation === 'row' ? 'row' : 'column');
    },
    axisReverse() {
      return (this.orientation === 'row' ? 'row-reverse' : 'column-reverse');
    },
    cross() {
      return (this.orientation === 'row' ? 'column' : 'row');
    },
    crossReverse() {
      return (this.orientation === 'row' ? 'column-reverse' : 'row-reverse');
    },
    nextIcon() {
      return (this.orientation === 'row' ? 'fa-arrow-right' : 'fa-arrow-down');
    },
    backIcon() {
      return (this.orientation === 'row' ? 'fa-arrow-left' : 'fa-arrow-up');
    },

  },
  watch: {
    orientation: 'setCssVars',
    // xray: 'setCssVars'
  },
  methods: {
    toggleModal(step) {
      step = step || 1;
      this.modal.isOpen = !this.modal.isOpen;
      if(this.modal.isOpen) {
        let self = this;
        setTimeout(function(){
          self.$sections = self.$el.querySelectorAll('section');
          self.max = self.$sections.length;
          self.goToStep(step);
        }, 1);
      } else {
        if (this.isLastStep) {
          window.SegMap.Tutorial.DoneWithTutorial();
        }
      }
    },
    noThanks() {
      if (window.SegMap) {
        window.SegMap.Tutorial.DoneWithTutorial();
      }
      this.toggleModal();
    },
    setCssVars(){
      this.$el.style.setProperty('--x', (((this.step * 100) - 100) * this.x_multiplier) + '%');
      this.$el.style.setProperty('--y', (((this.step * 100) - 100) * this.y_multiplier) + '%');
      this.$el.style.setProperty('--axis', this.axis);
      this.$el.style.setProperty('--axis-reverse', this.axisReverse);
      this.$el.style.setProperty('--cross', this.cross);
      this.$el.style.setProperty('--cross-reverse', this.crossReverse);
      // this.$el.style.setProperty('--vision', this.xray);
    },
    goToStep(step){
      this.step = step > this.max ? this.max : step < 1 ? 1 : step;
      this.currentSection = this.$sections[this.step - 1];
      this.$sections.forEach(function(section){
        section.classList.remove('current');
      });
      this.currentSection.classList.add('current');
      this.currentSection.scrollTop = 0;
      this.setCssVars();
    },
    reset(){
      this.goToStep(1);
    },
    finish(){
      this.toggleModal();
    }
  }
};
</script>

<style scoped lang="scss">
:root {
  --accent: #8fd1f2;
}

[v-cloak] {
  display: none;
}
.closeButton {
  float:right;
  margin-top: 3px;
  margin-right: 10px;
	cursor: pointer;
	cursor: hand;
}
.closeButton:hover {
  color: #888;
}

.halves {
  max-width: 350px;
  font-size: 14px;
  margin-left: 30px;
  float: left;
}
.topper {
position: absolute;
    top: 0;
    left: 0;
    background-color: #e61b1b;
    width: 100%;
    border-radius: 2px;
    color: white;
    padding: 8px;
    font-size: 26px;
}
// modal content sliders
article {
  flex: 1 1 100%;
  height: 100%;
  display: flex;
  flex-direction: var(--axis, row);
  overflow: hidden;
}
.articleContent {
  position: relative; padding-left: 18px; margin-right: 10px;
}
.articleTitle {
  padding: 6px 0 6px 12px; font-size: 27px;
  border-top-left-radius: 3px;
  border-top-right-radius: 3px;
  margin: -10px -10px 30px -10px;
  background-color: #00A0D2; color: #ffffff;
}
section p {
  font-size: 18px;
  padding-left: 0px;
}

section {
  width: 100%;
  visibility: hidden;
  flex: 0 0 100%;
  font-size: 18px;
  padding: 10px;
  overflow: auto;
  will-change: transform;
  transform: translate(var(--x, 0%), var(--y, 0%));
  transition: transform 300ms ease-out;
  position: relative;
  h2,h3,h4 {
    margin-top: 0;
  }
  &.current {
    visibility: visible;
  }
}
span.step {
  background: #808080;
  border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  color: #ffffff;
  display: inline-block;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 5px;
  text-align: center;
  width: 1.6em;
}

footer {
  position: relative;
  text-align: right;
  display: flex;
  flex-direction: var(--axis-reverse, row-reverse);
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 0 0 1px rgba(#000, .1);
  background: rgba(#000, .05);
  &:not(:empty) {
    padding: 1em;
  }
}

.step-dots {
  display: flex;
  flex-direction: var(--axis, row);
}
.step-dot {
  cursor: pointer;
  width: 1em;
  height: 1em;
  margin: .5ch;
  border-radius: 1em;
  background: currentColor;
  opacity: .2;
  transition: transform 100ms ease-out, opacity 150ms linear;
  &.active {
    opacity: .7;
    box-shadow: 0 0 1em -.25em;
  }
  &:hover {
    transform: scale(1.2)
  }
}
.forward-actions,
.back-actions {
  flex: 1;
  display: flex;
  flex-direction: var(--axis, row);
}
.forward-actions {
  justify-content: flex-end;
  // text-align: right;
}
.back-actions {
  justify-content: flex-start;
  // text-align: left;
}

// boring
*, *::before, *::after {box-sizing: border-box;}

a {
  color: var(--accent);
}

del {
  color: #ca1e34;
  font-style: italic;
}

p {
  line-height: 1.5;
}

body {
  margin: 0;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: sans-serif;
  background: snow;
  color: #333;
}

// broadal buttons
button {
  outline: none;
  font: inherit;
  line-height: 1;
  cursor: pointer;
  padding: .5em 1em;
  border-radius: .35em;
  color: rgba(#000, .7);
  background: rgba(#000, .1);
  border: 2px solid rgba(#000, .05);
  text-shadow: 0 1px 0 rgba(#fff, .4);
  transition: transform 50ms ease-out;
  will-change: transform;
  &:active {
    transform: scale(.98);
  }
  &:hover {
    color: blue!important;
    border-color: #c0c0c0!important;
  }
  &:focus {
    border-color: var(--accent);
    box-shadow: 0 0 1em 0 var(--accent);
  }
  &[disabled] {
    opacity: .2;
    cursor: not-allowed;
  }
  &.primary {
    border-color: transparent;
    background: transparent;
    font-weight: bold;
    &:not([disabled]) {
      color: var(--accent);
    }
  }
  &.accent {
    background: var(--accent);
    &:not([disabled]) {
      color: #666;
    }
  }
  &.secondary {
    border-color: transparent;
    background: transparent;
    &:not([disabled]) {
      color: rgba(#000, .4);
    }
  }
  &.cancel:not([disabled]) {
    color: var(--accent);
  }
}

</style>
