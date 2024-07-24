import './bootstrap'

import AOS from 'aos'
import 'aos/dist/aos.css'

window.AOS = AOS
document.addEventListener('DOMContentLoaded', () =>
    setTimeout(() => AOS.init({ duration: 800 }), 50),
)
