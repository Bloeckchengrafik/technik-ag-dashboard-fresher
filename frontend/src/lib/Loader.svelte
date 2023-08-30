<!-- Loader -->
<div class="loader">
    <div class="loader-blocks">
        <span class="block-1"></span>
        <span class="block-2"></span>
        <span class="block-3"></span>
        <span class="block-4"></span>
        <span class="block-5"></span>
        <span class="block-6"></span>
        <span class="block-7"></span>
        <span class="block-8"></span>
        <span class="block-9"></span>
        <span class="block-10"></span>
        <span class="block-11"></span>
        <span class="block-12"></span>
        <span class="block-13"></span>
        <span class="block-14"></span>
        <span class="block-15"></span>
        <span class="block-16"></span>
        <span class="block-17"></span>
        <span class="block-18"></span>
        <span class="block-19"></span>
        <span class="block-20"></span>
        <span class="block-21"></span>
        <span class="block-22"></span>
        <span class="block-23"></span>
        <span class="block-24"></span>
    </div>
</div>

<style lang="postcss">

    .loader {
        width: 0;
        height: 100px;
        scale: 0.5;
    }

    .loader-blocks {
        position: relative;
        width: 150px;
        height: 150px;
        margin: -75px 0 0 -75px;
    }

    .loader-blocks span {
        position: absolute;
        width: 20px;
        height: 20px;
        background: transparent;
        animation: loader-blocks 1.2s linear infinite;
    }

    .loader-blocks span:nth-child(1) {
        top: 0;
        left: 0;
        animation-delay: 0s;
    }

    .loader-blocks span:nth-child(2) {
        top: 0;
        left: 25px;
        animation-delay: 0.1s;
    }

    .loader-blocks span:nth-child(3) {
        top: 0;
        left: 50px;
        animation-delay: 0.2s;
    }

    .loader-blocks span:nth-child(4) {
        top: 0;
        left: 75px;
        animation-delay: 0.3s;
    }

    .loader-blocks span:nth-child(5) {
        top: 0;
        left: 100px;
        animation-delay: 0.4s;
    }

    .loader-blocks span:nth-child(6) {
        top: 0;
        left: 125px;
        animation-delay: 0.5s;
    }

    .loader-blocks span:nth-child(7) {
        top: 0;
        left: 150px;
        animation-delay: 0.6s;
    }

    .loader-blocks span:nth-child(8) {
        top: 25px;
        left: 150px;
        animation-delay: 0.7s;
    }

    .loader-blocks span:nth-child(9) {
        top: 50px;
        left: 150px;
        animation-delay: 0.8s;
    }

    .loader-blocks span:nth-child(10) {
        top: 75px;
        left: 150px;
        animation-delay: 0.9s;
    }

    .loader-blocks span:nth-child(11) {
        top: 100px;
        left: 150px;
        animation-delay: 1s;
    }

    .loader-blocks span:nth-child(12) {
        top: 125px;
        left: 150px;
        animation-delay: 1.1s;
    }

    .loader-blocks span:nth-child(13) {
        top: 150px;
        left: 150px;
        animation-delay: 1.2s;
    }

    .loader-blocks span:nth-child(14) {
        top: 150px;
        left: 125px;
        animation-delay: 1.3s;
    }

    .loader-blocks span:nth-child(15) {
        top: 150px;
        left: 100px;
        animation-delay: 1.4s;
    }

    .loader-blocks span:nth-child(16) {
        top: 150px;
        left: 75px;
        animation-delay: 1.5s;
    }

    .loader-blocks span:nth-child(17) {
        top: 150px;
        left: 50px;
        animation-delay: 1.6s;
    }

    .loader-blocks span:nth-child(18) {
        top: 150px;
        left: 25px;
        animation-delay: 1.7s;
    }

    .loader-blocks span:nth-child(19) {
        top: 150px;
        left: 0;
        animation-delay: 1.8s;
    }

    .loader-blocks span:nth-child(20) {
        top: 125px;
        left: 0;
        animation-delay: 1.9s;
    }

    .loader-blocks span:nth-child(21) {
        top: 100px;
        left: 0;
        animation-delay: 2s;
    }

    .loader-blocks span:nth-child(22) {
        top: 75px;
        left: 0;
        animation-delay: 2.1s;
    }

    .loader-blocks span:nth-child(23) {
        top: 50px;
        left: 0;
        animation-delay: 2.2s;
    }

    .loader-blocks span:nth-child(24) {
        top: 25px;
        left: 0;
        animation-delay: 2.3s;
    }

    @keyframes loader-blocks {
        0% {
            transform: scale(1);
            background: transparent;
        }

        50% {
            transform: scale(1.5);
            @apply bg-primary;
        }

        100% {
            transform: scale(1);
            background: transparent;
        }
    }
</style>