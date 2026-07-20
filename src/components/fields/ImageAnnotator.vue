<template>
  <div @click="addMarker" style="position: relative;" ref="imageContainer">
    <img :src="imageUrl" class="annotator-image" style="width:100%; user-select: none; -webkit-user-drag: none;" />
    <k-pin-icon
      v-for="(marker, identifier) in markers"
      @mousedown.native.stop="markerDown(identifier, $event)"
      :key="identifier"
      :style="{
        position:'absolute',
        left:(marker.x)+'%',
        top:(marker.y)+'%',
        transform:'translate(0px, -40px)',
      }"
    >{{ identifier+1 }}</k-pin-icon>
  </div>    
</template>

<script>
export default {
  props: {
    markers: Array,
    imageUrl: String
  },
  data() {
    return {
      draggingMarkerIndex: null,
      isDragging: false,
    };
  },
  methods: {
    // when someone clicks somewhere on the image add a marker (get the place of where the image is and then calculate where the click was calculated from the boundaries of the image)
    addMarker(e) {
      if (this.isDragging) return;

      const { x, y } = this.calculateMarkerposition(e);

      const newMarkers = [
        ...this.markers,
        { x, y, zoom: 0 }
      ];

      this.$emit("update:markers", newMarkers);
    },
    // when someone tries to click (drag) on an already excisting marker start a function to update the values of the marker
    markerDown(identifier, e) {
      e.preventDefault();
      this.draggingMarkerIndex = identifier;
      this.isDragging = true;

      window.addEventListener('mousemove', this.markerMove);
      window.addEventListener('mouseup', this.markerUp);
    },
    markerMove(e) {
      if (this.draggingMarkerIndex == null) return;

      let { x, y } = this.calculateMarkerposition(e);
      
      // the marker can not go outside the image
      x = this.boundries(x);
      y = this.boundries(y);

      const updated = [...this.markers];
      updated[this.draggingMarkerIndex] = {
        ...updated[this.draggingMarkerIndex],
        x,
        y
      };

      this.$emit("update:markers", updated);

    }, 
    calculateMarkerposition(e) {
      const rect = this.$refs.imageContainer.getBoundingClientRect();
      let x = ((e.clientX - rect.left) / rect.width) * 100;
      let y = ((e.clientY - rect.top) / rect.height) * 100;

      return {
        x: Math.min(100, Math.max(0, Math.round(x))),
        y: Math.min(100, Math.max(0, Math.round(y)))
      };
    },  
    boundries(value) {
      return Math.min(100, Math.max(0, value));
    },
    // when user release the mouse after moving
    markerUp() {
      this.draggingMarkerIndex = null;

      window.removeEventListener('mousemove', this.markerMove);
      window.removeEventListener('mouseup', this.markerUp);

      setTimeout(() => {
        this.isDragging = false;
      }, 0);
    },
  },
}
</script>

<style>

</style>