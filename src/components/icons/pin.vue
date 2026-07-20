<template>
  <div class="pin-pointer"> 
    <div class="pin-pointer-content">
      <svg width="73" height="73" viewBox="0 0 73 73" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 30C0 13.4315 13.4315 0 30 0H43C59.5685 0 73 13.4315 73 30V43C73 59.5685 59.5685 73 43 73H11C4.92487 73 0 68.0751 0 62V30Z" fill="#177BA6"/>
      </svg>
      <p class="pin-content"><slot></slot></p>
    </div>
  </div>
</template>

<style>
  
.pin-pointer-content {
  position: relative;
  width: 40px;
  height: 40px;
}

.pin-pointer svg {
  display: block;
}

.pin-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  
  display: flex;
  align-items: center;
  justify-content: center;

  color: white; 
  font-size: 24px;
  font-weight: bold;
}

.pin-pointer {
  cursor: grab;
}

.pin-pointer:active {
  cursor: grabbing;
}


</style>