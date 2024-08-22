import './bootstrap'

import AOS from 'aos'

window.AOS = AOS
document.addEventListener('DOMContentLoaded', () =>
    setTimeout(() => AOS.init({ duration: 800 }), 50),
)
