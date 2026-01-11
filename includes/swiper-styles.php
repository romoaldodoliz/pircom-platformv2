<style>
/* Swiper Hero Slider */
.swiper-container {
    width: 100%;
    height: 100vh;
}

.swiper-slide {
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.swiper-slide::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(21, 21, 21, 0.4);
}

.swiper-slide h2 {
    position: relative;
    z-index: 1;
    color: white;
    font-size: 48px;
    font-weight: 800;
    text-shadow: 3px 3px 8px rgba(0,0,0,0.7);
    padding: 0 20px;
    text-align: center;
}

.swiper-button-prev,
.swiper-button-next {
    color: var(--primary-color) !important;
    background: rgba(255,255,255,0.9);
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

.swiper-button-prev::after,
.swiper-button-next::after {
    font-size: 20px;
}

.swiper-pagination-bullet-active {
    background: var(--primary-color) !important;
}

.social-icons a {
    color: var(--secondary-color);
    font-size: 20px;
    margin: 0 10px;
    transition: color 0.3s;
    text-decoration: none;
}

.social-icons a:hover {
    color: var(--primary-color);
}

.services {
    background: #f9f9f9;
    padding: 80px 0;
}

.features {
    padding: 80px 0;
}

.cta {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0a0a0aff 50%, var(--secondary-color) 100%);
    padding: 80px 0;
    color: white;
}

.cta h3 {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 20px;
}

.cta-btn {
    background: white;
    color: var(--primary-color);
    padding: 15px 40px;
    border-radius: 30px;
    font-weight: 700;
    text-decoration: none;
    display: inline-block;
    margin-top: 20px;
    transition: all 0.3s;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.cta-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    color: var(--primary-color);
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: var(--primary-color);
    border-radius: 50%;
    padding: 20px;
}

@media (max-width: 768px) {
    .swiper-slide h2 {
        font-size: 32px;
    }
}
</style>
