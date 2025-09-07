<script setup>
import { ref } from 'vue';


const images = []

if (images.length === 0) {
    console.warn('No images found for scrolling effect.');
    images.push('/images/aboutScroll/placeholder.webp');
}


const duplicatedImages = ref([...images, ...images, ...images, ...images])
while (duplicatedImages.value.length < 20) {
    duplicatedImages.value = [...duplicatedImages.value, ...duplicatedImages.value];
}

const halfwayPoint = - (duplicatedImages.value.length / 2) * 191;

</script>

<style>

.about {
        justify-items: center;
        margin: auto;
        padding: 0px 50px 50px;
        max-width: 1540px;
    }


.about > h1 {
  font-size: 2.4em;
  font-weight: 900;
  margin: 50px 0 30px;
  width: 60%;
  line-height: 1em;
}

.about > div {
    display: flex;
    width: 80%;
    padding-top: 20px;
    gap: 20px;
}

ul {
    list-style: disc;
    list-style-position: inside;
}
ul li {
    list-style-type: disc;
}


.scrolling-container {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
}

.scrolling-content {
  animation: scroll-left 20s linear infinite;
}

.scrolling-content img {
    height: 207px;
    width: 161px;
    margin-right: 30px;
    display: inline-block;
    object-fit: cover;
}

.evenImage {
    transform: translateY(50px);
}

@keyframes scroll-left {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(var(--halfway-point));
  }
}

</style>

<template>
    <div>

    <div class="about" >
        <h1>CREATIVITY WITHOUT BORDERS. CONTENT THAT CAPTIVATES, INSPIRES, AND REFUSES TO BE IGNORED.</h1>
        <div>
            <p style="flex: 2;" >
                <strong>Welcome to Mozgi AI </strong>- the creative playground where ideas have no limits. We produce commercials, reels, music videos, animated shorts, and films powered by AI and human ingenuity. Our collective of bold minds is driven by one mission: to make content that captivates, inspires, and leaves a mark.
            </p>
            <div style="flex: 1;">With us, you get:<br>
                <ul>
                    <li>Any format, any platform</li>
                    <li>Speed that matches your ambition</li>
                    <li>Freedom from traditional barriers</li>
                </ul>
            </div>
            <p style="flex: 1;"><strong>Mozgi AI:</strong> Turning imagination into impact.</p>
        </div>
    </div>

    <div class="scrolling-container"
    :style="{ '--halfway-point': halfwayPoint + 'px' }">
        <div class="scrolling-content">
            <img 
            v-for="(img, index) in duplicatedImages" 
            :class="index % 2 === 0 ? '' : 'evenImage'"
            :key="index"
            :src="img" />
        </div>
    </div>
    </div>
</template>