<template>
  <div v-if="image" class="logoDiv">
    <img class="logoIcon" ref="watermarkImg" :src="image" />
  </div>
</template>

<script>
import axios from "axios";
import err from "@/common/js/err";

export default {
  name: "logoFloat",
  props: ["work"],
  data() {
    alert("1");
    return {
      image: null
    };
  },
  mounted() {
    alert("2");
    // Obtiene el file de la Imagen
    this.GetWatermarkImage();
  },
  methods: {
    GetWatermarkImage() {
      const loc = this;
      return axios
        .get(/*window.host + */ "/services/backoffice/GetWatermarkImage", {
          params: { wmi: loc.work.Current.WatermarkId }
        })
        .then(function(res) {
          alert("3");
          loc.image = res.data;
        })
        .catch(function(error) {
          err.errDialog(
            "GetWatermarkImage",
            "obtener el logo de la institución"
          );
        });
    }
    /*
    onResize() {
      var visible = this.work.Current !== null;
      if (visible) {
        this.updateWork();
      }
    },
    updateWork() {
      var visible = this.work.Current !== null;
      var logo = document.getElementById("logoFloatIcon");
      if (visible) {
        if (window.SegMap) {
          window.SegMap.TriggerResize();
        }
      } else {
        this.work.Current = null;
        if (window.SegMap) {
          window.SegMap.SaveRoute.RemoveWork();
          window.SegMap.TriggerResize();
        }
      }
    }*/
  }
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

