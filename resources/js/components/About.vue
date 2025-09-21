<script setup>
import { computed, inject, onMounted, ref, nextTick } from 'vue';

const content = inject('content');
const images = computed (() => content.images.length ? content.images.map(img => img.img_url) : ['/storage/aboutScroll/placeholder.jpg']);
const duplicatedImages = computed (() => {
    let result = [...images.value, ...images.value, ...images.value, ...images.value];
    
    while (result.length < 20) {
        result = [...result, ...result];
    }
    
    return result;
})

const halfwayPoint = - (duplicatedImages.value.length / 2) * 191;

const timePerLoop = (duplicatedImages.value.length) * 1.75; // seconds
const isEntering = ref(true);

onMounted(async () => {

    // To make sure that everything is loaded before starting the animation.
    await nextTick();

  requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            isEntering.value = false;
        });
  });
})

</script>

<template>
    <div class="container pb-[150px] desktop:pb-0 page-transition" :class="{ 'page-enter-from': isEntering }" >
        <h1>CREATIVITY WITHOUT BORDERS. CONTENT THAT CAPTIVATES, INSPIRES, AND REFUSES TO BE IGNORED.</h1>
        <div>
            <p style="flex: 2;">
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
            <button><p>CONTACT US</p><img src="/public/images/arrowRW.png" style="width: 30px;" alt=""></button>
        </div>
    </div>

    <div class="scrolling-container page-transition" :class="{ 'page-enter-from': isEntering }"
    :style="{ '--halfway-point': halfwayPoint + 'px', '--time-per-loop': timePerLoop + 's' }">
        <div class="scrolling-content">
            <img v-for="(img, index) in duplicatedImages" :class="index % 2 === 0 ? '' : 'evenImage'" :key="index" :src="img" />
        </div>
    </div>
</template>

<style scoped>

.container > h1 {
    color: #262626;
    font-size: 34px;
    margin: 50px 0 30px;
    width: 65%;
	font-variation-settings: "wdth" 125;
	font-weight: var(--font-weight-bold);
	letter-spacing: 0;
	line-height: 1;
	text-transform: uppercase;
}

.container > div {
    display: flex;
    width: 80%;
    padding-top: 20px;
    gap: 20px;

    color: #262626;
    font-size: 15px;
	font-variation-settings: "wdth" 125;
	line-height: 1.5;
    opacity: 0.9;
}

ul {
    list-style: disc;
    list-style-position: inside;
}
ul li {
    list-style-type: disc;
}


.scrolling-container {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
}

.scrolling-content {
  animation: scroll-left var(--time-per-loop) linear infinite;
}

.scrolling-content img {
    height: 180px;
    width: 140px;
    margin-right: 30px;
    display: inline-block;
    object-fit: cover;
}

.evenImage {
    transform: translateY(50px);
}

button {
    display: none
}

@keyframes scroll-left {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(var(--halfway-point));
  }
}


@media (max-width: 1440px) {

.container {
    padding-bottom: 150px;
}

.container > h1 {
	font-size: 20px;
    margin: 0;
    width: 100%;
}

.container > div {
    font-size: 13px;
    display: flex;
    flex-direction: column;
    width: 100%;
    gap: 30px;
}

button {
	align-items: center;
	background: black;
	border-radius: 32px;
	color: white;
	display: flex;
	font-size: 20px;
	font-variation-settings: "wdth" 125;
	font-weight: var(--font-weight-bold);
	justify-content: space-between;
	padding: 18px 26px 18px 30px;
	transition: all 0.3s ease;
	width: 100%;
}

button:hover {
    background: rgba(0, 0, 0, 0.8);
}

}

</style>