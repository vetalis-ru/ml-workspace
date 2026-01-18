<?php // css template ?>
<style>
.wpm-settings-form {
  display: none;
}
.mbl-options-preloader {
  width: 100%;
  height: 200px;
  display: flex;
  justify-content: center;
  align-items: center;
}
/* loader-ellipse
------------------------- */
@keyframes reveal {
  from {
    transform: scale(0.001);
  }
  to {
    transform: scale(1);
  }
}

@keyframes slide {
  to {
    transform: translateX(1.5em);
  }
}

.loader-ellipse {
  font-size: 20px;
  position: relative;
  width: 4em;
  height: 1em;
  margin: 10px auto;
}
.loader-ellipse__dot {
  display: block;
  width: 1em;
  height: 1em;
  border-radius: 0.5em;
  background: #555;
  position: absolute;
  animation-duration: 0.5s;
  animation-timing-function: ease;
  animation-iteration-count: infinite;
}
.loader-ellipse__dot:nth-child(1) {
  left: 0;
  animation-name: reveal;
}
.loader-ellipse__dot:nth-child(2) {
  left: 0;
  animation-name: slide;
}
.loader-ellipse__dot:nth-child(3) {
  left: 1.5em;
  animation-name: slide;
}
.loader-ellipse__dot:nth-child(4) {
  left: 3em;
  animation-name: reveal;
  animation-direction: reverse;
}
.loader-ellipse.small {
  font-size: 10px;
}
.loader-ellipse.small .loader-ellipse__dot:nth-child(3) {
  left: 1em;
}
.loader-ellipse.small .loader-ellipse__dot:nth-child(4) {
  left: 2em;
}


</style>
