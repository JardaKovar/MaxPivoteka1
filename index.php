<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, maximum-scale=5.0">
    <title>MAX PIVOTÉKA</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MAX PIVOTÉKA">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Playfair+Display:wght@400;500;700&family=Georgia:wght@400&display=swap" rel="stylesheet">
    <style>
/* Reset default styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Global styles */
body {
  font-family: 'Playfair Display', Georgia, serif;
  line-height: 1.6;
  background: linear-gradient(rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.85)), url('images/tapeta-red-seamless.jpg');
  background-repeat: repeat;
  background-size: 1000px auto;
  background-attachment: scroll;
  color: #fff;
  position: relative;
}

/* Navigation styles */
.main-nav {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  transition: transform 0.3s ease;
}

.main-nav.hidden {
  transform: translateY(-100%);
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0.5rem 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  background: rgba(0, 0, 0, 0.8);
}

.nav-container img {
  height: auto;
  width: auto;
  max-width: 100%;
  max-height: 100px;
}

@media (max-width: 1200px) {
  .nav-container img {
    max-height: 80px;
  }
}

.nav-toggle {
  display: none;
  flex-direction: column;
  justify-content: space-around;
  width: 30px;
  height: 24px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
  z-index: 1100;
}

.nav-toggle:focus {
  outline: none;
}

.nav-toggle span {
  width: 30px;
  height: 3px;
  background: #fff;
  border-radius: 2px;
  transition: all 0.3s linear;
  position: relative;
  transform-origin: 1px;
}

.nav-toggle.active span:nth-child(1) {
  transform: rotate(45deg);
}

.nav-toggle.active span:nth-child(2) {
  opacity: 0;
  transform: translateX(20px);
}

.nav-toggle.active span:nth-child(3) {
  transform: rotate(-45deg);
}

.nav-links {
  display: flex;
  gap: 2rem;
}

.nav-links a {
  text-decoration: none;
  color: #fff;
  font-weight: 500;
  font-size: 1rem;
  transition: color 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.nav-links a:hover {
  color: #ccc;
}

/* Hero section */
.hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: clamp(5rem, 10vh, 7rem) 1.5rem 4rem 1.5rem;
  position: relative;
  box-sizing: border-box;
}

.hero-content {
  max-width: 1000px;
  width: 100%;
  margin: 0;
  padding: 0;
  text-align: left;
  opacity: 0;
  transform: translateX(-50px);
  animation: appearFromLeft 1s ease forwards;
}


.hero-social-icons {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin-top: clamp(1rem, 2.5vh, 2rem);
  flex-wrap: wrap;
}

.hero-social-icons a {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
}

.hero-social-icons img {
  height: 36px;
  width: auto;
  transition: transform 0.3s ease;
  object-fit: contain;
}

.hero-social-icons img.partner-icon {
  height: 38px;
  max-width: 150px;
}

.hero-social-icons img:hover {
  transform: scale(1.1);
}


@keyframes appearFromLeft {
  from {
    opacity: 0;
    transform: translateX(-50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.hero h1 {
  font-size: clamp(1.4rem, 2.4vw + 0.6rem, 2.5rem);
  margin-bottom: clamp(1rem, 2.5vh, 2rem);
  line-height: 1.35;
  font-weight: 700;
  word-wrap: break-word;
}

.hero h1 span { font-size: inherit; font-weight: inherit; margin-top: 0; display: inline; }

.hero p {
  font-size: clamp(1.05rem, 1.2vw + 0.4rem, 1.4rem);
  margin-bottom: 0.75rem;
  color: #ccc;
}

.hero-buttons {
  margin: clamp(1rem, 3vh, 2rem) 0;
  display: flex;
  justify-content: flex-start;
  flex-wrap: wrap;
  gap: 1rem;
}

.btn {
  display: inline-block;
  padding: 1rem 2rem;
  text-decoration: none;
  border-radius: 5px;
  font-weight: 500;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.btn.primary {
  background-color: #fff;
  color: #000;
}

.btn.secondary {
  background-color: transparent;
  border: 2px solid #fff;
  color: #fff;
}

.social-links {
  margin-top: 3rem;
}

.social-links h3 {
  margin-bottom: 1rem;
  font-size: 1.2rem;
  color: #ccc;
}

.social-icons {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.social-icon {
  width: 40px;
  height: 40px;
  transition: transform 0.3s ease;
  filter: brightness(0) invert(1);
}

.social-icon:hover {
  transform: scale(1.1);
}

/* Section styles */
section {
  padding: 6rem 1rem;
  margin: 0 auto;
  max-width: 1200px;
  position: relative;
}

section h2 {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 3rem;
  position: relative;
  display: inline-block;
  width: 100%;
}

section h2:after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 2px;
  background: #fff;
}

/* About section */
.about-container {
  display: flex;
  align-items: center;
  gap: 4rem;
}

.about-image {
  flex: 1;
}

.about-image img {
  width: 100%;
  height: auto;
  border-radius: 10px;
}

.about-text {
  flex: 1;
   max-width: 800px;
  margin: 0 auto;
  line-height: 1.6;
  text-align: justify;
}

/* Tap section */
.tap-container {
  overflow-x: auto;
  padding: 2rem 0;
  -webkit-overflow-scrolling: touch;
}

/* Modern Clean Table Styling */
.tap-list {
  width: 100%;
  min-width: 800px;
  border-collapse: collapse;
  font-family: 'Playfair Display', Georgia, serif;
  margin: 2rem 0;
  border-radius: 10px;
  overflow: hidden;
}

.tap-list th {
  padding: 1.2rem 0.8rem;
  text-align: center;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-size: 0.9rem;
  border-right: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  white-space: nowrap;
}

.tap-list td {
  padding: 1rem 0.8rem;
  text-align: center;
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  background: transparent;
  transition: background-color 0.3s ease;
  white-space: nowrap;
  font-size: 0.9rem;
}

.tap-list tbody tr:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

.tap-list tbody tr:nth-child(even) {
}

/* Events section - Compact simple grey */
.events-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1.5rem;
  max-width: 900px;
  margin: 0 auto;
  width: 100%;
}

.event-card {
  width: 100%;
  max-width: 420px;
  background: #2b2b2b !important;
  padding: 1.5rem 1.8rem;
  border-radius: 8px;
  border: 1px solid #444444 !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  text-align: left;
  box-sizing: border-box;
}

.event-date {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.4rem;
  color: #ffffff !important;
}

.event-card h3 {
  font-size: 1.15rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #ffffff !important;
}

.event-card p {
  color: #cccccc !important;
  font-size: 0.95rem;
  line-height: 1.5;
  margin: 0;
}

/* Rental section */
.rental-container {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.rental-list {
  width: 100%;
  min-width: 900px;
  border-collapse: collapse;
  font-family: 'Playfair Display', Georgia, serif;
  margin: 2rem 0;
  border-radius: 10px;
  overflow: hidden;
}

.rental-list th {
  padding: 1.2rem 0.8rem;
  text-align: center;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-size: 0.9rem;
  border-right: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  white-space: nowrap;
}

.rental-list td {
  padding: 1rem 0.8rem;
  text-align: center;
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  background: transparent;
  transition: background-color 0.3s ease;
  white-space: nowrap;
  font-size: 0.9rem;
}

.rental-list tbody tr:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

.rental-list tbody tr:nth-child(even) {
}

.rental-list td img {
  max-height: 80px;
  width: auto;
  display: block;
  margin: 0 auto;
  transition: transform 0.3s ease;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
}

.rental-list tr:hover img {
  transform: scale(1.1) rotate(-5deg);
}

.rental-contact {
  text-align: center;
  margin-top: 1.5rem;
  font-size: 1.1rem;
  color: #fff;
}

/* Delivery section */
.delivery-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 4rem;
}

.delivery-text {
  flex: 1;
}

.delivery-item {
  flex: 1;
}

@keyframes carDriveIn {
  0% {
    transform: translateX(-100%) scale(0.9);
    opacity: 0;
  }
  60% {
    transform: translateX(20px) scale(1);
    opacity: 1;
  }
  80% {
    transform: translateX(-10px) scale(1);
  }
  100% {
    transform: translateX(0) scale(1);
    opacity: 1;
  }
}

.delivery-item img {
  width: 100%;
  height: auto;
  display: block;
  opacity: 0;
  transform: translateX(-100%) scale(0.9);
}

.delivery-item img.car-animation {
  animation: carDriveIn 1.5s ease-out forwards;
}

/* Gallery section */

.gallery-empty-notice {
  column-span: all !important;
  -webkit-column-span: all !important;
  text-align: center !important;
  color: #94a3b8 !important;
  font-size: 1.1rem !important;
  padding: 2.5rem 1rem !important;
  width: 100% !important;
  display: block !important;
}

.gallery-container {
  column-count: 4;
  column-gap: 1rem;
  padding: 0;
}

@media (max-width: 1200px) {
  .gallery-container {
    column-count: 3;
  }
}

@media (max-width: 768px) {
  .gallery-container {
    column-count: 2;
  }
}

@media (max-width: 480px) {
  .gallery-container {
    column-count: 1;
  }
}

.gallery-item {
  position: relative;
  display: inline-block;
  width: 100%;
  margin-bottom: 1rem;
  break-inside: avoid;
  overflow: hidden;
  border-radius: 10px;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.gallery-item::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) scale(0.8);
  width: 40px;
  height: 40px;
  background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>') center/contain no-repeat;
  opacity: 0;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 2;
}

.gallery-item:hover::before {
  opacity: 1;
  transform: translate(-50%, -50%) scale(1);
}

.gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-item:hover img {
  transform: scale(1.1);
  filter: brightness(0.7);
}

.gallery-modal {
  display: flex;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.95);
  z-index: 9999;
  justify-content: center;
  align-items: center;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-modal.active {
  opacity: 1;
  visibility: visible;
}

.gallery-modal img {
  max-width: 85%;
  max-height: 85vh;
  object-fit: contain;
  transform: scale(0.9);
  opacity: 0;
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
  pointer-events: auto;
  position: relative;
  z-index: 10000;
}

.gallery-modal.active img {
  transform: scale(1);
  opacity: 1;
}

.gallery-modal-content {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  max-width: 90%;
  max-height: 90%;
}

.gallery-nav {
  position: fixed;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.8);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.3);
  font-size: 2.5rem;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  z-index: 10001;
  user-select: none;
  font-weight: bold;
}

.gallery-nav:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.6);
  transform: translateY(-50%) scale(1.1);
}

.gallery-nav:active {
  transform: translateY(-50%) scale(0.95);
}

.gallery-prev {
  left: 30px;
}

.gallery-next {
  right: 30px;
}

.gallery-modal-close {
  position: fixed;
  color: white;
  font-size: 30px;
  cursor: pointer;
  background: rgba(0, 0, 0, 0.7);
  border: none;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 10002;
  top: 20px;
  right: 20px;
}

.gallery-modal-close:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.1);
}

.gallery-counter {
  position: fixed;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 12px 24px;
  border-radius: 25px;
  font-size: 1.2rem;
  z-index: 10001;
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255, 255, 255, 0.3);
  font-weight: bold;
}

@keyframes appleEffect {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(0.95);
  }
  100% {
    transform: scale(1);
  }
}

.gallery-item img.apple-effect {
  animation: appleEffect 0.3s ease;
}

/* Contact section */
.display_custom {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.tel, .mail, .adresa {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  border-radius: 10px;
}

.icon svg {
  width: 24px;
  height: 24px;
  stroke: #fff;
}

.tel a, .mail a, .adresa a {
  color: #fff;
  text-decoration: none;
  transition: color 0.3s ease;
}

.tel a:hover, .mail a:hover, .adresa a:hover {
  color: #ccc;
}

.opening-hours {
  max-width: 300px;
  margin: 0;
  padding: 2rem;
}

.opening-hours span {
  display: flex;
  justify-content: space-between;
  margin: 0.5rem 0;
  color: #fff;
}

/* Map container */
.map-container {
  width: 100%;
  max-width: 600px;
  margin: 0;
  padding: 2rem;
  border-radius: 10px;
  overflow: hidden;
}

.map-container iframe {
  width: 100%;
  height: 400px;
  border: none;
  display: block;
  border-radius: 10px;
}

/* Map and opening hours container */
.map-opening-container {
  display: flex;
  gap: 2rem;
  justify-content: center;
  align-items: flex-start;
  flex-wrap: wrap;
  margin-top: 2rem;
}

.map-opening-container .map-container {
  flex: 1 1 400px;
  max-width: 600px;
  margin: 0;
  padding: 0;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.map-opening-container .opening-hours {
  flex: 1 1 300px;
  max-width: 300px;
  margin: 0;
  padding: 2rem;
  color: #fff;
}

/* Footer */
footer {
  text-align: center;
  padding: 2rem;
  color: #fff;
}

/* Mobile Navigation - Hamburger Menu */
.nav-toggle {
  display: none;
  background: none;
  border: none;
  color: #fff;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
  z-index: 1001;
}

.nav-toggle span {
  display: block;
  width: 25px;
  height: 3px;
  background: #fff;
  margin: 5px 0;
  transition: 0.3s;
}

.nav-toggle.active span:nth-child(1) {
  transform: rotate(-45deg) translate(-5px, 6px);
}

.nav-toggle.active span:nth-child(2) {
  opacity: 0;
}

.nav-toggle.active span:nth-child(3) {
  transform: rotate(45deg) translate(-5px, -6px);
}


/* Short Viewport Laptop Optimization */
@media (max-height: 800px) {
  .hero {
    min-height: auto;
    padding-top: 6rem;
    padding-bottom: 3rem;
  }
  .hero h1 {
    font-size: clamp(1.3rem, 2.2vw + 0.5rem, 2.1rem);
    margin-bottom: 1rem;
  }
  .hero h1 span { font-size: inherit; font-weight: inherit; margin-top: 0; display: inline; }
  .hero p {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
  }
  .hero-buttons {
    margin: 1.2rem 0;
  }
}

/* Mobile-First Responsive Design */

/* Large Tablets and Small Desktops */
@media (max-width: 1024px) {
  section {
    padding: 4rem 1rem;
  }
  
  .hero h1 { font-size: clamp(1.4rem, 2.4vw + 0.6rem, 2.5rem); }
  
  .tap-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .tap-list, .rental-list {
    min-width: 800px;
  }
  
  .gallery-modal img {
    max-width: 90%;
    max-height: 80vh;
  }
  
  .gallery-nav {
    width: 50px;
    height: 50px;
    font-size: 2rem;
  }
  
  .gallery-prev {
    left: 20px;
  }
  
  .gallery-next {
    right: 20px;
  }
}

/* Tablets */
@media (max-width: 768px) {
  /* Navigation Mobile */
  .nav-container {
    padding: 1rem;
    position: relative;
  }
  
  .nav-toggle {
    display: block;
  }
  
  .nav-links {
    position: fixed;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100vh;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(10px);
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    transition: left 0.3s ease;
    z-index: 1000;
  }
  
  .nav-links.active {
    left: 0;
  }
  
  .nav-links a {
    font-size: 1.5rem;
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    width: 80%;
    text-align: center;
  }
  
  /* Hero Section Mobile */
  .hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: clamp(5rem, 10vh, 7rem) 1.5rem 4rem 1.5rem;
  position: relative;
  box-sizing: border-box;
}

.hero-content {
  max-width: 1000px;
  width: 100%;
  margin: 0;
  padding: 0;
  text-align: left;
  opacity: 0;
  transform: translateX(-50px);
  animation: appearFromLeft 1s ease forwards;
}


.hero-social-icons {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin-top: clamp(1rem, 2.5vh, 2rem);
  flex-wrap: wrap;
}

.hero-social-icons a {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
}

.hero-social-icons img {
  height: 36px;
  width: auto;
  transition: transform 0.3s ease;
  object-fit: contain;
}

.hero-social-icons img.partner-icon {
  height: 38px;
  max-width: 150px;
}

.hero-social-icons img:hover {
  transform: scale(1.1);
}


@keyframes appearFromLeft {
  from {
    opacity: 0;
    transform: translateX(-50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.hero h1 {
  font-size: clamp(1.4rem, 2.4vw + 0.6rem, 2.5rem);
  margin-bottom: clamp(1rem, 2.5vh, 2rem);
  line-height: 1.35;
  font-weight: 700;
  word-wrap: break-word;
}

.hero h1 span { font-size: inherit; font-weight: inherit; margin-top: 0; display: inline; }

.hero p {
  font-size: clamp(1.05rem, 1.2vw + 0.4rem, 1.4rem);
  margin-bottom: 0.75rem;
  color: #ccc;
}

.hero-buttons {
  margin: clamp(1rem, 3vh, 2rem) 0;
  display: flex;
  justify-content: flex-start;
  flex-wrap: wrap;
  gap: 1rem;
}
  
  .btn {
    padding: 1.2rem 2rem;
    font-size: 1rem;
    width: 100%;
    max-width: 300px;
    text-align: center;
  }
  
  /* Sections Mobile */
  section {
    padding: 3rem 1rem;
  }
  
  section h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
  }
  
  /* About Section Mobile */
  .about-container {
    flex-direction: column;
    gap: 2rem;
    text-align: center;
  }
  
  /* Tables Mobile - Horizontal Scroll */
  .tap-container {
    margin: 0 -1rem;
    padding: 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .tap-list, .rental-list {
    min-width: 900px;
    font-size: 0.9rem;
  }
  
  .tap-list th, .rental-list th {
    padding: 0.8rem 0.5rem;
    font-size: 0.8rem;
    white-space: nowrap;
  }
  
  .tap-list td, .rental-list td {
    padding: 0.8rem 0.5rem;
    font-size: 0.85rem;
    white-space: nowrap;
  }
  
  .tap-list th:first-child,
  .tap-list td:first-child,
  .rental-list th:first-child,
  .rental-list td:first-child {
    position: sticky;
    left: 0;
    z-index: 10;
    box-shadow: 2px 0 4px rgba(0,0,0,0.3);
  }
  
  /* Events Mobile */
  .events-container {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .event-card {
    padding: 1.5rem;
    text-align: center;
  }
  
  /* Delivery Mobile */
  .delivery-container {
    flex-direction: column;
    gap: 2rem;
    text-align: center;
  }
  
  .delivery-text {
    order: 2;
  }
  
  .delivery-item {
    order: 1;
  }
  
  /* Contact Mobile */
  .display_custom {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .tel, .mail, .adresa {
    padding: 1rem;
    text-align: center;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .map-opening-container {
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .map-container {
    padding: 0;
  }
  
  .map-container iframe {
    height: 300px;
  }
  
  .opening-hours {
    padding: 1.5rem;
    text-align: center;
  }
  
  /* Gallery Mobile */
  .gallery-modal img {
    max-width: 95%;
    max-height: 70vh;
  }
  
  .gallery-nav {
    width: 45px;
    height: 45px;
    font-size: 1.8rem;
  }
  
  .gallery-prev {
    left: 15px;
  }
  
  .gallery-next {
    right: 15px;
  }
  
  .gallery-modal-close {
    top: 15px;
    right: 15px;
    width: 45px;
    height: 45px;
    font-size: 25px;
  }
  
  .gallery-counter {
    bottom: 20px;
    padding: 10px 20px;
    font-size: 1rem;
  }
}

/* Mobile Phones */
@media (max-width: 480px) {
  /* Navigation Ultra Mobile */
  .nav-container img {
    max-height: 60px;
  }
  
  .nav-links a {
    font-size: 1.3rem;
    padding: 1.2rem;
  }
  
  /* Hero Ultra Mobile */
  .hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: clamp(5rem, 10vh, 7rem) 1.5rem 4rem 1.5rem;
  position: relative;
  box-sizing: border-box;
}

.hero-content {
  max-width: 1000px;
  width: 100%;
  margin: 0;
  padding: 0;
  text-align: left;
  opacity: 0;
  transform: translateX(-50px);
  animation: appearFromLeft 1s ease forwards;
}


.hero-social-icons {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin-top: clamp(1rem, 2.5vh, 2rem);
  flex-wrap: wrap;
}

.hero-social-icons a {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
}

.hero-social-icons img {
  height: 36px;
  width: auto;
  transition: transform 0.3s ease;
  object-fit: contain;
}

.hero-social-icons img.partner-icon {
  height: 38px;
  max-width: 150px;
}

.hero-social-icons img:hover {
  transform: scale(1.1);
}


@keyframes appearFromLeft {
  from {
    opacity: 0;
    transform: translateX(-50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.hero h1 {
  font-size: clamp(1.4rem, 2.4vw + 0.6rem, 2.5rem);
  margin-bottom: clamp(1rem, 2.5vh, 2rem);
  line-height: 1.35;
  font-weight: 700;
  word-wrap: break-word;
}

.hero h1 span { font-size: inherit; font-weight: inherit; margin-top: 0; display: inline; }

.hero p {
  font-size: clamp(1.05rem, 1.2vw + 0.4rem, 1.4rem);
  margin-bottom: 0.75rem;
  color: #ccc;
}

.hero-buttons {
  margin: clamp(1rem, 3vh, 2rem) 0;
  display: flex;
  justify-content: flex-start;
  flex-wrap: wrap;
  gap: 1rem;
}
  
  section {
    padding: 2rem 1rem;
  }
  
  .gallery-modal img {
    max-height: 70vh;
  }
  
  .gallery-counter {
    bottom: 10px;
  }
}

/* High DPI Display Optimization */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .nav-container img,
  .hero-social-icons img,
  .gallery-item img {
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
  }
}

/* Reduced Motion for Accessibility */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  
  .hero-content {
    animation: none;
    opacity: 1;
    transform: none;
  }
  
  .delivery-item img.car-animation {
    animation: none;
    opacity: 1;
    transform: none;
  }
}

/* Enhanced 3D Coverflow Pricing Section - Much Larger */
.pricing-container {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  max-width: 100%;
  margin: 0 auto;
  padding: 3rem 1rem;
}

.coverflow-container {
  position: relative;
  width: 100%;
  max-width: 1600px;
  height: 700px;
  margin: 3rem auto;
  perspective: 2000px;
  overflow: visible;
}

.coverflow-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  transform-style: preserve-3d;
  transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.coverflow-item {
  position: absolute;
  width: 315px;
  height: 420px;
  cursor: pointer;
  transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  transform-style: preserve-3d;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
  border: 3px solid rgba(255, 255, 255, 0.1);
}

.coverflow-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 17px;
  transition: all 0.8s ease;
  filter: brightness(0.8) contrast(0.9);
}

.coverflow-item.active {
  transform: translateX(0) translateZ(150px) rotateY(0deg) scale(1.3);
  z-index: 10;
  box-shadow: 0 40px 80px rgba(0, 0, 0, 0.7);
  border-color: rgba(255, 255, 255, 0.3);
}

.coverflow-item.active img {
  filter: brightness(1.2) contrast(1.2);
}

.coverflow-item.prev-2 {
  transform: translateX(-700px) translateZ(-250px) rotateY(55deg) scale(0.75);
  opacity: 0.6;
  z-index: 2;
}

.coverflow-item.prev-1 {
  transform: translateX(-350px) translateZ(-120px) rotateY(35deg) scale(0.95);
  opacity: 0.85;
  z-index: 6;
}

.coverflow-item.next-1 {
  transform: translateX(350px) translateZ(-120px) rotateY(-35deg) scale(0.95);
  opacity: 0.85;
  z-index: 6;
}

.coverflow-item.next-2 {
  transform: translateX(700px) translateZ(-250px) rotateY(-55deg) scale(0.75);
  opacity: 0.6;
  z-index: 2;
}

.coverflow-item.hidden {
  transform: translateX(1000px) translateZ(-400px) rotateY(-70deg) scale(0.6);
  opacity: 0.3;
  z-index: 1;
}

.coverflow-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
  margin-top: 2rem;
}

.coverflow-nav {
  background: rgba(255, 255, 255, 0.1);
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: white;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 1.5rem;
  font-weight: bold;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.coverflow-nav:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.6);
  transform: scale(1.1);
}

.coverflow-nav:active {
  transform: scale(0.95);
}

.coverflow-indicators {
  display: flex;
  gap: 0.5rem;
}

.coverflow-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  cursor: pointer;
  transition: all 0.3s ease;
}

.coverflow-dot.active {
  background: rgba(255, 255, 255, 0.9);
  transform: scale(1.2);
}

.coverflow-dot:hover {
  background: rgba(255, 255, 255, 0.6);
}

.pricing-modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.95);
  z-index: 9999;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.pricing-modal.active {
  display: flex;
  opacity: 1;
}

.pricing-modal img {
  max-width: 90%;
  max-height: 90vh;
  object-fit: contain;
  border-radius: 15px;
  box-shadow: 0 0 50px rgba(0, 0, 0, 0.8);
}

.pricing-modal-close {
  position: fixed;
  top: 30px;
  right: 30px;
  background: rgba(0, 0, 0, 0.8);
  color: white;
  border: none;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  font-size: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.pricing-modal-close:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.1);
}

/* Mobile Responsive for Coverflow */
@media (max-width: 768px) {
  .coverflow-container {
    height: 300px;
    perspective: 800px;
  }
  
  .coverflow-item {
    width: 200px;
    height: 250px;
  }
  
  .coverflow-item.prev-2,
  .coverflow-item.next-2 {
    transform: translateX(-300px) translateZ(-150px) rotateY(45deg) scale(0.6);
  }
  
  .coverflow-item.prev-1 {
    transform: translateX(-150px) translateZ(-75px) rotateY(25deg) scale(0.8);
  }
  
  .coverflow-item.next-1 {
    transform: translateX(150px) translateZ(-75px) rotateY(-25deg) scale(0.8);
  }
  
  .coverflow-nav {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
  }
}

@media (max-width: 480px) {
  .coverflow-container {
    height: 250px;
    perspective: 600px;
  }
  
  .coverflow-item {
    width: 160px;
    height: 200px;
  }
  
  .coverflow-item.active {
    transform: translateX(0) translateZ(0) rotateY(0deg) scale(1.05);
  }
  
  .coverflow-item.prev-1 {
    transform: translateX(-120px) translateZ(-60px) rotateY(30deg) scale(0.75);
  }
  
  .coverflow-item.next-1 {
    transform: translateX(120px) translateZ(-60px) rotateY(-30deg) scale(0.75);
  }
  
  .coverflow-item.prev-2,
  .coverflow-item.next-2,
  .coverflow-item.hidden {
    display: none;
  }
  
  .coverflow-controls {
    gap: 1rem;
    margin-top: 1rem;
  }
  
  .coverflow-nav {
    width: 45px;
    height: 45px;
    font-size: 1rem;
  }
}

/* Keg Pricing Section Styles */
.pricing-section,
.price-list-wrapper,
.keg-pricing-container {
  background: transparent !important;
  box-shadow: none !important;
  border: none !important;
}

.keg-pricing-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 2.5rem;
  padding: 1.5rem 1rem 3rem 1rem;
  margin: 0 auto;
}

.keg-wrapper {
  position: relative;
  display: inline-block;
  cursor: pointer;
  user-select: none;
  transition: transform 0.3s ease;
  background: transparent !important;
  box-shadow: none !important;
  border: none !important;
}

.keg-wrapper:hover {
  transform: scale(1.05);
}

.keg-image {
  width: 100%;
  max-width: 220px;
  height: auto;
  display: block;
  filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.8));
  background: transparent !important;
}

.keg-text-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 70%;
  text-align: center;
  color: #000000;
  pointer-events: none;
}

.keg-title {
  display: block;
  font-family: 'Outfit', 'Playfair Display', sans-serif;
  font-size: 1.15rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #000000;
  text-shadow: none;
  line-height: 1.2;
}

/* Keg Shake Animation */
.keg-shake {
  animation: kegShake 0.6s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes kegShake {
  10%, 90% { transform: scale(1.05) translate3d(-3px, 0, 0) rotate(-3deg); }
  20%, 80% { transform: scale(1.05) translate3d(5px, 0, 0) rotate(4deg); }
  30%, 50%, 70% { transform: scale(1.05) translate3d(-7px, 0, 0) rotate(-5deg); }
  40%, 60% { transform: scale(1.05) translate3d(7px, 0, 0) rotate(5deg); }
}

/* Wrapper to align price list and gallery vertically with centered gallery */
.price-list-gallery-wrapper {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto 4rem auto;
  padding: 2rem 1rem;
  position: relative;
  left: 20px; /* Move the whole wrapper 20px to the right */
}

/* Remove section padding for wrapped sections */
.price-list-gallery-wrapper section {
  padding: 2rem 0;
  margin: 0;
  max-width: none;
}

/* Gallery container adjustment for vertical layout */
.price-list-gallery-wrapper .gallery-container {
  column-count: 3;
  column-gap: 1rem;
  max-width: 1200px;
  margin: 0 auto;
}

@media (max-width: 900px) {
  .price-list-gallery-wrapper .gallery-container {
    column-count: 2;
  }
}

@media (max-width: 600px) {
  .price-list-gallery-wrapper .gallery-container {
    column-count: 1;
  }
}

/* Pricing section adjustment for vertical layout */
.price-list-gallery-wrapper .pricing-section {
  max-width: 600px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Pricing container adjustment */
.price-list-gallery-wrapper .pricing-container {
  max-width: 100%;
  box-sizing: border-box;
}

/* Responsive adjustments */
@media (max-width: 900px) {
  .price-list-gallery-wrapper {
    max-width: 100%;
    left: 0; /* Remove the offset on mobile */
  }
  .price-list-gallery-wrapper .gallery-container,
  .price-list-gallery-wrapper .pricing-section {
    max-width: 100%;
  }
}

/* Reservation Form Styles */
.reservation-section {
  margin-top: 4rem;
  padding: 3rem 2rem;
  border-radius: 15px;
}

.reservation-section h3 {
  font-size: 2rem;
  margin-bottom: 1rem;
  text-align: center;
  color: #fff;
  position: relative;
}

.reservation-section h3:after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 2px;
  background: #fff;
}

.reservation-section p {
  text-align: center;
  margin-bottom: 2rem;
  color: #ccc;
  font-size: 1.1rem;
}

.reservation-form {
  max-width: 800px;
  margin: 0 auto;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 0.5rem;
  color: #fff;
  font-weight: 500;
  font-size: 1rem;
}

.form-group input,
.form-group textarea,
.form-group select {
  padding: 1rem;
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  font-size: 1rem;
  font-family: 'Playfair Display', Georgia, serif;
  transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: rgba(255, 255, 255, 0.5);
  background: rgba(255, 255, 255, 0.15);
  box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
}

.form-group select option {
  background: #333;
  color: #fff;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

.reservation-btn {
  width: 100%;
  max-width: 300px;
  margin: 2rem auto 0;
  display: block;
  position: relative;
  cursor: pointer;
  border: none;
  font-family: 'Playfair Display', Georgia, serif;
}

.reservation-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-loading {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.reservation-message {
  margin-top: 1.5rem;
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
  font-weight: 500;
}

.reservation-message.success {
  background: rgba(40, 167, 69, 0.2);
  border: 1px solid rgba(40, 167, 69, 0.5);
  color: #28a745;
}

.reservation-message.error {
  background: rgba(220, 53, 69, 0.2);
  border: 1px solid rgba(220, 53, 69, 0.5);
  color: #dc3545;
}

/* Guaranteed pure white calendar picker icon for date inputs */
.form-group input[type="date"]::-webkit-calendar-picker-indicator,
input[type="date"]::-webkit-calendar-picker-indicator {
  filter: brightness(0) invert(1) !important;
  -webkit-filter: brightness(0) invert(1) !important;
  cursor: pointer !important;
  opacity: 1 !important;
}

/* Mobile Responsive for Reservation Form */
@media (max-width: 768px) {
  .reservation-section {
    margin-top: 3rem;
    padding: 2rem 1rem;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
  }
  
  .form-group input,
  .form-group textarea {
    padding: 0.8rem;
    font-size: 0.9rem;
  }
  
  .reservation-section h3 {
    font-size: 1.5rem;
  }
  
  .reservation-section p {
    font-size: 1rem;
  }
}

@media (max-width: 480px) {
  .reservation-section {
    padding: 1.5rem 0.5rem;
  }
  
  .form-group input,
  .form-group textarea {
    padding: 0.7rem;
    font-size: 0.85rem;
  }
  
  .reservation-btn {
    max-width: 100%;
  }
}
    
/* Site Pop-up Announcement Overlay */
.site-popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.85);
  backdrop-filter: blur(8px);
  z-index: 99999;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1.5rem;
  box-sizing: border-box;
  animation: fadeInPopup 0.3s ease;
}

@keyframes fadeInPopup {
  from { opacity: 0; }
  to { opacity: 1; }
}

.site-popup-card {
  background: #111827;
  border: 2px solid #ef4444;
  border-radius: 16px;
  max-width: 550px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7);
  color: #fff;
  text-align: center;
  animation: scaleInPopup 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scaleInPopup {
  from { transform: scale(0.85); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.site-popup-close {
  position: absolute;
  top: 12px;
  right: 16px;
  background: rgba(255, 255, 255, 0.15);
  border: none;
  color: #fff;
  font-size: 28px;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.site-popup-close:hover {
  background: rgba(239, 68, 68, 0.8);
  transform: scale(1.1);
}

#site-popup-image-container img {
  width: 100%;
  max-height: 280px;
  object-fit: cover;
  border-top-left-radius: 14px;
  border-top-right-radius: 14px;
  display: block;
}

.site-popup-body {
  padding: 2rem 1.8rem;
}

.site-popup-body h2 {
  font-size: 1.8rem;
  margin-bottom: 1rem;
  color: #fff;
  font-family: 'Playfair Display', Georgia, serif;
}

.site-popup-body p {
  font-size: 1.15rem;
  line-height: 1.6;
  color: #d1d5db;
  margin-bottom: 1.8rem;
  white-space: pre-line;
}

.site-popup-footer .btn {
  padding: 0.8rem 2.2rem;
  font-size: 1rem;
  cursor: pointer;
}

</style>
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <img src="images/logo2.png" alt="MAX PIVOTÉKA">
            <button class="nav-toggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="nav-links">
                <a href="#O-nas">O nás</a>
                <a href="#Prave-na-cepu">Právě na čepu</a>
                <a href="#Akce">Akce</a>
                <a href="#Cenik">Ceník</a>
                <a href="#Pujcovna">Půjčovna</a>
                <a href="#Rozvoz">Rozvoz</a>
                <a href="#Kontakty">Kontakty</a>
                <a href="#Galerie">Galerie</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Prodejna s pivem, vínem, alkoholem a pochutinami<br>
            600druhů piva ze 150ti pivovarů<br>
            Beer to go - čepujeme pivo do lahví na počkání<br>
            Půjčovna výčepního zařízení</h1>
            <div class="hero-buttons">
                <a href="#Prave-na-cepu" class="btn primary">Právě na čepu</a>
                <a href="#Kontakty" class="btn secondary">Jak se k nám dostanete</a>
            </div>
            <div class="hero-social-icons">
                <a href="https://www.facebook.com/odmaxbenesov/?fref=ts" target="_blank" title="Facebook MAX PIVOTÉKA"><img src="images/facebook.png" alt="Facebook"></a>
                <a href="https://www.instagram.com/max.pivoteka/" target="_blank" title="Instagram MAX PIVOTÉKA"><img src="images/instagram.png" alt="Instagram"></a>
                <a href="https://maxbeerbar.cz/" target="_blank" title="Max Beer Bar"><img src="images/maxbeerbar.png" alt="Max Beer Bar" class="partner-icon"></a>
                <a href="https://nasladovce.cz/" target="_blank" title="Koupaliště Na Sladovce"><img src="images/nasladovce.png" alt="Na Sladovce" class="partner-icon"></a>
            </div>
        </div>
    </section>

    <section id="O-nas" class="about-section">
        <div class="about-container">
            <div class="about-image">
                <img src="images/pivo.png" alt="Pivo">
            </div>
            <div class="about-text">
                <h2>O nás</h2>
                <p>
MAX PIVOTÉKA v Benešově nabízí jeden z nejširších výběrů piv v regionu – více než 600 druhů spodně i svrchně kvašených speciálů, sezónních edic i limitovaných várek z menších i větších pivovarů.<br><br>
K pivu u nás najdete také české cidery, kvalitní rumy, likéry a vína od malých českých vinařů, stejně jako oblíbené pochutiny – nakládané sýry, utopence či pravé české brambůrky.<br><br>
Nabízíme i rozvoz zboží, zapůjčení výčepního zařízení, prodej potravinářských plynů Drinkgas a komisní prodej.<br><br>
Naším cílem je nabízet kvalitní produkty a férové ceny ke každému zákazníkovi.
                </p>

            </div>
        </div>
    </section>

<section id="Prave-na-cepu" class="tap-section">
    <h2>Právě na čepu</h2>
    <div class="tap-container">
        <table class="tap-list" id="tap-list-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Pivovar</th>
                    <th>Pivo</th>
                    <th>Alk. %</th>
                    <th>EPM</th>
                    <th>0,5l (Kč)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tapDataFile = __DIR__ . '/data/taplist.json';
                $tapList = file_exists($tapDataFile) ? json_decode(file_get_contents($tapDataFile), true) : [];
                if (!empty($tapList)):
                    foreach ($tapList as $index => $tap):
                        if (!empty($tap['brewery']) || !empty($tap['beer'])):
                ?>
                <tr>
                    <td><?= htmlspecialchars($tap['number'] ?? ($index + 1)) ?></td>
                    <td><?= htmlspecialchars($tap['brewery'] ?? '') ?></td>
                    <td><?= htmlspecialchars($tap['beer'] ?? '') ?></td>
                    <td><?= htmlspecialchars($tap['alc'] ?? '') ?></td>
                    <td><?= htmlspecialchars($tap['epm'] ?? '') ?></td>
                    <td><?= htmlspecialchars($tap['price_05l'] ?? '') ?></td>
                </tr>
                <?php
                        endif;
                    endforeach;
                endif;
                ?>
            </tbody>
        </table>
    </div>
</section>

<section id="Akce" class="events-section">
    <h2>Akce</h2>
    <div class="events-container" id="events-container">
        <?php
        $eventsDataFile = __DIR__ . '/data/events.json';
        $events = file_exists($eventsDataFile) ? json_decode(file_get_contents($eventsDataFile), true) : [];
        if (!empty($events)):
            foreach ($events as $event):
                if (!empty($event['title']) || !empty($event['description'])):
        ?>
        <div class="event-card">
            <?php if (!empty($event['date'])): ?>
                <div class="event-date"><?= htmlspecialchars($event['date']) ?></div>
            <?php endif; ?>
            <?php if (!empty($event['title'])): ?>
                <h3><?= htmlspecialchars($event['title']) ?></h3>
            <?php endif; ?>
            <?php if (!empty($event['description'])): ?>
                <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
            <?php endif; ?>
        </div>
        <?php
                endif;
            endforeach;
        endif;
        ?>
    </div>
</section>

    <div class="price-list-wrapper">
      <section id="Cenik" class="pricing-section">
          <h2>Ceník</h2>
          <div class="keg-pricing-container" id="keg-pricing-container">
              <?php
              $cenikDataFile = __DIR__ . '/data/cenik.json';
              $cenikList = file_exists($cenikDataFile) ? json_decode(file_get_contents($cenikDataFile), true) : [];
              if (isset($cenikList['title']) && !isset($cenikList[0])) {
                  $cenikList = [['id' => '1', 'title' => $cenikList['title'], 'pdf' => $cenikList['pdf'] ?? 'uploads/cenik.pdf']];
              }
              if (empty($cenikList)) {
                  $cenikList = [['id' => '1', 'title' => 'Ceník Srpen', 'pdf' => 'uploads/cenik.pdf']];
              }
              foreach ($cenikList as $item):
                  $title = htmlspecialchars($item['title'] ?? 'Ceník');
                  $pdf = htmlspecialchars($item['pdf'] ?? 'uploads/cenik.pdf');
              ?>
              <div class="keg-wrapper" onclick="downloadKegPdf(this, '<?= $pdf ?>', '<?= $title ?>')" title="Klikněte pro otevření ceníku <?= $title ?>">
                  <img src="images/keg_cenik.png" alt="Pivní sud Ceník" class="keg-image">
                  <div class="keg-text-overlay">
                      <span class="keg-title"><?= $title ?></span>
                  </div>
              </div>
              <?php endforeach; ?>
          </div>
      </section>
    </div>

    <script>
    function downloadKegPdf(el, pdfUrl, title) {
        if (!el) return;
        el.classList.remove('keg-shake');
        void el.offsetWidth;
        el.classList.add('keg-shake');
        setTimeout(() => {
            const link = document.createElement('a');
            link.href = pdfUrl || 'uploads/cenik.pdf';
            link.download = (title || 'Cenik') + '.pdf';
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }, 350);
    }
    </script>

<section id="Pujcovna" class="rental-section">
        <h2>Půjčovna</h2>
        <div class="rental-container">
            <table class="rental-list">
                <thead>
                    <tr>
                        <th></th>
                        <th>Popis</th>
                        <th>Obrázek</th>
                        <th>Popis</th>
                        <th>Kauce</th>
                        <th>Den</th>
                        <th>Víkend</th>
                        <th>Týden</th>
                        <th>Měsíc</th>
                    </tr>
                </thead>
                <tbody id="rental-list-tbody">
                <?php
                $rentalDataFile = __DIR__ . '/data/rentallist.json';
                $rentalList = file_exists($rentalDataFile) ? json_decode(file_get_contents($rentalDataFile), true) : [];
                if (!empty($rentalList)):
                    foreach ($rentalList as $index => $rental):
                        if (!empty($rental['desc1']) || !empty($rental['desc2'])):
                ?>
                <tr>
                    <td><?= htmlspecialchars($rental['number'] ?? ($index + 1)) ?></td>
                    <td><?= htmlspecialchars($rental['desc1'] ?? '') ?></td>
                    <td>
                        <?php if (!empty($rental['image'])): ?>
                            <img src="<?= htmlspecialchars(strpos($rental['image'], 'images/') === 0 ? $rental['image'] : 'images/pujcovna/' . $rental['image']) ?>" alt="<?= htmlspecialchars($rental['desc1'] ?? 'Půjčovna') ?>" class="rental-image">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($rental['desc2'] ?? '') ?></td>
                    <td><?= htmlspecialchars($rental['deposit'] ?? '') ?></td>
                    <td><?= htmlspecialchars($rental['day'] ?? '') ?></td>
                    <td><?= htmlspecialchars($rental['weekend'] ?? '') ?></td>
                    <td><?= htmlspecialchars($rental['week'] ?? '') ?></td>
                    <td><?= htmlspecialchars($rental['month'] ?? '') ?></td>
                </tr>
                <?php
                        endif;
                    endforeach;
                endif;
                ?>
            </tbody>
            </table>
            <p class="rental-contact">Pro zapůjčení prosím volat na <a href="tel:+420731179453" style="color: inherit; text-decoration: none;">+420 731 179 453</a> / <a href="tel:+420317721341" style="color: inherit; text-decoration: none;">+420 317 721 341</a></p>
        </div>
        
        <!-- Rental Reservation Form -->
        <div class="reservation-section">
            <h3>Poptávka půjčovny</h3>
            <p>Chcete si půjčit naše vybavení? Vyplňte formulář níže a my se Vám ozveme s potvrzením a detaily.</p>
            
            <form id="reservation-form" class="reservation-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Jméno *</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Příjmení *</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="tel" id="phone" name="phone" placeholder="+420 XXX XXX XXX">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="rental_item">Půjčovní předmět *</label>
                        <select id="rental_item" name="rental_item" required>
                            <option value="">Vyberte předmět</option>
                            <!-- Options will be dynamically loaded -->
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="rental_date_from">Datum od *</label>
                        <input type="date" id="rental_date_from" name="rental_date_from" required>
                    </div>
                    <div class="form-group">
                        <label for="rental_date_to">Datum do *</label>
                        <input type="date" id="rental_date_to" name="rental_date_to" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="additional_info">Dodatečné informace</label>
                    <textarea id="additional_info" name="additional_info" rows="4" placeholder="Zde můžete uvést jakékoli speciální požadavky, místo doručení, nebo další poznámky..."></textarea>
                </div>



                <button type="submit" class="btn primary reservation-btn">
                    <span class="btn-text">Odeslat poptávku</span>
                    <span class="btn-loading" style="display: none;">Odesílám...</span>
                </button>
            </form>
            
            <div id="reservation-message" class="reservation-message" style="display: none;"></div>
        </div>
    </section>

    <section id="Rozvoz" class="delivery-section">
        <h2>Rozvoz</h2>
        <div class="delivery-container">
            <div class="delivery-text">
                <p>Pro rozvoz nás kontaktujte na tel. <a href="tel:+420731179453" style="color: inherit; text-decoration: none;">731 179 453</a> nebo na email <a href="mailto:obchod@maxpivoteka.cz" style="color: inherit; text-decoration: none;">obchod@maxpivoteka.cz</a></p>
                <p style="font-weight: bold; margin-top: 0.6rem; font-size: 1.25rem;">!! jeden den předem !!</p>
            </div>
            <div class="delivery-item">
                <img src="images/dodavka2.png" alt="Rozvoz" loading="lazy" class="car-animation">
            </div>
        </div>
    </section>

    <section id="Kontakty" class="contact-section">
        <h2>Kontakty</h2>
        <div class="display_custom">
            <div class="tel">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </div>
                <div style="white-space: nowrap;">
                    <div style="margin-bottom: 0.35rem; white-space: nowrap;">
                        <a href="tel:+420731179453" style="white-space: nowrap;">+420 731 179 453</a> <span style="font-size: 0.95rem; color: #ccc; white-space: nowrap;">– Marek Skořepa (Vedoucí prodejny)</span>
                    </div>
                    <div style="margin-bottom: 0.35rem; white-space: nowrap;">
                        <a href="tel:+420317721341" style="white-space: nowrap;">+420 317 721 341</a> <span style="font-size: 0.95rem; color: #ccc; white-space: nowrap;">– Prodejna</span>
                    </div>
                    <div style="white-space: nowrap;">
                        <a href="tel:+420603239703" style="white-space: nowrap;">+420 603 239 703</a> <span style="font-size: 0.95rem; color: #ccc; white-space: nowrap;">– Petr Pokorný (Majitel)</span>
                    </div>
                </div>
            </div>
            <div class="mail">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <div>
                    <span>odmax@seznam.cz</span>
                    <br>
                    <div class="d-flex align-items-center">
                        <a href="mailto:odmax@seznam.cz" target="_blank" class="d-flex align-items-center">
                            Napište nám
                        </a>
                    </div>
                </div>
            </div>
            <div class="adresa">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <div>
                    <span>Červené Vršky 2086, Benešov</span>
                    <br>
                    <div class="d-flex align-items-center">
                        <a href="https://goo.gl/maps/nouaD9nVMjUjMeie7" target="_blank" class="d-flex align-items-center">
                            Navigovat
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="map-opening-container">
            <div class="opening-hours" style="text-align: center;">
                <h2>Otevírací doba</h2>
                <div style="font-size: 1.4rem; font-weight: bold; margin-top: 1rem; color: #fff;">Po – Ne</div>
                <div style="font-size: 1.25rem; margin-top: 0.4rem; color: #ccc;">8:00 – 19:00</div>
            </div>
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2570.8234567890123!2d14.6871234567890!3d49.7812345678901!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x470b93d1234567890%3A0x1234567890abcdef!2zQ2VydmVuw6kgVnLFoWt5IDIwODYsIDI1NjAxIEJlbmXFoW92!5e0!3m2!1scs!2scz!4v1234567890123!5m2!1scs!2scz"
                    width="100%" 
                    height="400" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Mapa MAX PIVOTÉKA - Červené Vršky 2086, Benešov">
                </iframe>
            </div>
        </div>
    </section>

    <section id="Galerie" class="gallery-section">
        <h2>Galerie</h2>
        <div class="gallery-container">
            <?php
            $galleryDir = __DIR__ . '/images/gallery/';
            $gImages = [];
            if (is_dir($galleryDir)) {
                $files = array_diff(scandir($galleryDir), ['.', '..']);
                foreach ($files as $f) {
                    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $gImages[] = $f;
                    }
                }
                sort($gImages);
            }
            if (!empty($gImages)):
                foreach ($gImages as $index => $img):
            ?>
            <div class="gallery-item">
                <img src="images/gallery/<?= htmlspecialchars($img) ?>" alt="Galerie <?= $index + 1 ?>" loading="lazy">
            </div>
            <?php 
                endforeach;
            else:
            ?>
            <p class="gallery-empty-notice">V galerii zatím nejsou žádné fotky.</p>
            <?php endif; ?>
        </div>
    </section>

    <div class="gallery-modal">
        <button class="gallery-nav gallery-prev" aria-label="Previous image">&#8249;</button>
        <div class="gallery-modal-content">
            <img src="" alt="Enlarged gallery image">
        </div>
        <button class="gallery-nav gallery-next" aria-label="Next image">&#8250;</button>
        <button class="gallery-modal-close" aria-label="Close image">&times;</button>
        <div class="gallery-counter">
            <span class="current-image">1</span> / <span class="total-images">8</span>
        </div>
    </div>

    <!-- Pricing Modal -->
    <div class="pricing-modal" id="pricing-modal">
        <button class="gallery-nav gallery-prev" id="pricing-prev" aria-label="Previous price list">&#8249;</button>
        <div class="gallery-modal-content">
            <img src="" alt="Price List" id="pricing-modal-img">
        </div>
        <button class="gallery-nav gallery-next" id="pricing-next" aria-label="Next price list">&#8250;</button>
        <button class="pricing-modal-close" id="pricing-modal-close" aria-label="Close price list">&times;</button>
        <div class="gallery-counter">
            <span class="current-image" id="pricing-current">1</span> / <span class="total-images" id="pricing-total">1</span>
        </div>
    </div>

    <footer style="text-align: center; padding: 2.5rem 1rem; background: transparent; color: #cbd5e1; font-family: sans-serif;">
        <p>&copy; 2026 MAX PIVOTÉKA. Všechna práva vyhrazena. <span class="artist-signature" style="font-family: 'Caveat', cursive; font-size: 1.5rem; font-weight: 700; font-style: italic; color: #ff4d5e; margin-left: 1.2rem; display: inline-block; transform: rotate(-5deg); text-shadow: 0 0 10px rgba(255, 77, 94, 0.5); border-bottom: 2px solid #ff4d5e;">Pokorney</span></p>
    </footer>
    <script src="js/nav-visibility.js"></script>
    <script src="js/smooth-scroll.js"></script>
    <script src="js/gallery-effects.js"></script>
    <script src="js/delivery-animation.js"></script>
    <script src="js/load-dashboard-data.js"></script>
    
    <script>
    // Mobile Navigation Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const navToggle = document.querySelector('.nav-toggle');
        const navLinks = document.querySelector('.nav-links');
        const navContainer = document.querySelector('.nav-container');
        
        if (navToggle && navLinks) {
            navToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');
                navToggle.classList.toggle('active');
                navContainer.classList.toggle('mobile-open');
                
                // Prevent body scroll when menu is open
                if (navLinks.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
            
            // Close menu when clicking on a link
            const navLinkItems = navLinks.querySelectorAll('a');
            navLinkItems.forEach(link => {
                link.addEventListener('click', function() {
                    navLinks.classList.remove('active');
                    navToggle.classList.remove('active');
                    navContainer.classList.remove('mobile-open');
                    document.body.style.overflow = '';
                });
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!navContainer.contains(event.target) && navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    navToggle.classList.remove('active');
                    navContainer.classList.remove('mobile-open');
                    document.body.style.overflow = '';
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    navLinks.classList.remove('active');
                    navToggle.classList.remove('active');
                    navContainer.classList.remove('mobile-open');
                    document.body.style.overflow = '';
                }
            });
        }

        // Load rental list data
        function loadRentalList() {
            fetch('get_rentallist.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('rental-list-tbody');
                    const rentalSelect = document.getElementById('rental_item');
                    
                    if (tbody && Array.isArray(data)) {
                        let html = '';
                        data.forEach(item => {
                            const imageSrc = item.image ? `images/${item.image}` : 'images/default.png';
                            html += `
                                <tr>
                                    <td>${item.number}</td>
                                    <td>${item.desc1 || ''}</td>
                                    <td><img src="${imageSrc}" alt="${item.desc1}" style="max-height: 80px;" onerror="this.style.display='none'"></td>
                                    <td>${item.desc2 || ''}</td>
                                    <td>${item.deposit || ''}</td>
                                    <td>${item.day || ''}</td>
                                    <td>${item.weekend || ''}</td>
                                    <td>${item.week || ''}</td>
                                    <td>${item.month || ''}</td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                    }
                    
                    // Populate rental item select
                    if (rentalSelect && Array.isArray(data)) {
                        let options = '<option value="">Vyberte předmět</option>';
                        data.forEach(item => {
                            if (item.desc1 && item.desc2) {
                                options += `<option value="${item.desc1} - ${item.desc2}">${item.desc1} - ${item.desc2}</option>`;
                            }
                        });
                        rentalSelect.innerHTML = options;
                    }
                })
                .catch(error => {
                    console.error('Error loading rental list:', error);
                });
        }

        // Price calculation for rental form
        function calculateRentalPrice() {
            const rentalItemSelect = document.getElementById('rental_item');
            const rentalPeriodSelect = document.getElementById('rental_period');
            const priceDisplay = document.getElementById('price-display');
            const totalPriceSpan = document.getElementById('total-price');
            const depositAmountSpan = document.getElementById('deposit-amount');

            if (!rentalItemSelect || !rentalPeriodSelect || !priceDisplay) return;

            const selectedItem = rentalItemSelect.value;
            const selectedPeriod = rentalPeriodSelect.value;

            if (!selectedItem || !selectedPeriod) {
                priceDisplay.style.display = 'none';
                return;
            }

            // Find the selected item in the rental data
            fetch('get_rentallist.php')
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data)) {
                        const item = data.find(item => `${item.desc1} - ${item.desc2}` === selectedItem);
                        if (item) {
                            const deposit = parseInt(item.deposit) || 0;
                            let rentalPrice = 0;

                            switch (selectedPeriod) {
                                case 'den':
                                    rentalPrice = parseInt(item.day) || 0;
                                    break;
                                case 'vikend':
                                    rentalPrice = parseInt(item.weekend) || 0;
                                    break;
                                case 'tyden':
                                    rentalPrice = parseInt(item.week) || 0;
                                    break;
                                case 'mesic':
                                    rentalPrice = parseInt(item.month) || 0;
                                    break;
                            }

                            const totalPrice = deposit + rentalPrice;

                            totalPriceSpan.textContent = totalPrice.toLocaleString('cs-CZ');
                            depositAmountSpan.textContent = deposit.toLocaleString('cs-CZ');
                            priceDisplay.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error calculating price:', error);
                    priceDisplay.style.display = 'none';
                });
        }



        // 3D Coverflow Functionality
        let coverflowImages = [];
        let currentIndex = 0;

        // Load pricing images and initialize coverflow
        function loadPricingImages() {
            fetch('get_cenik_images.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.images && data.images.length > 0) {
                        coverflowImages = data.images;
                        initializeCoverflow();
                    } else {
                        // Show fallback if no images
                        document.getElementById('coverflow-container').style.display = 'none';
                        document.querySelector('.coverflow-controls').style.display = 'none';
                        document.getElementById('pricing-image-container').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading pricing images:', error);
                    // Show fallback on error
                    document.getElementById('coverflow-container').style.display = 'none';
                    document.querySelector('.coverflow-controls').style.display = 'none';
                    document.getElementById('pricing-image-container').style.display = 'block';
                });
        }

        // Initialize the 3D coverflow
        function initializeCoverflow() {
            if (coverflowImages.length === 0) return;

            const wrapper = document.getElementById('coverflow-wrapper');
            const indicators = document.getElementById('coverflow-indicators');
            
            // Clear existing content
            wrapper.innerHTML = '';
            indicators.innerHTML = '';

            // Create coverflow items
            coverflowImages.forEach((image, index) => {
                const item = document.createElement('div');
                item.className = 'coverflow-item';
                item.innerHTML = `<img src="images/cenik/${image}" alt="Price List ${index + 1}">`;
                item.addEventListener('click', () => {
                    if (index === currentIndex) {
                        openPricingModal(image);
                    } else {
                        goToSlide(index);
                    }
                });
                wrapper.appendChild(item);

                // Create indicator dot
                const dot = document.createElement('div');
                dot.className = 'coverflow-dot';
                dot.addEventListener('click', () => goToSlide(index));
                indicators.appendChild(dot);
            });

            // Set initial positions
            updateCoverflowPositions();

            // Add navigation event listeners
            document.getElementById('coverflow-prev').addEventListener('click', () => {
                goToSlide(currentIndex > 0 ? currentIndex - 1 : coverflowImages.length - 1);
            });

            document.getElementById('coverflow-next').addEventListener('click', () => {
                goToSlide(currentIndex < coverflowImages.length - 1 ? currentIndex + 1 : 0);
            });

            // Add keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    goToSlide(currentIndex > 0 ? currentIndex - 1 : coverflowImages.length - 1);
                } else if (e.key === 'ArrowRight') {
                    goToSlide(currentIndex < coverflowImages.length - 1 ? currentIndex + 1 : 0);
                }
            });
        }

        // Go to specific slide
        function goToSlide(index) {
            currentIndex = index;
            updateCoverflowPositions();
        }

        // Update coverflow positions
        function updateCoverflowPositions() {
            const items = document.querySelectorAll('.coverflow-item');
            const dots = document.querySelectorAll('.coverflow-dot');

            items.forEach((item, index) => {
                // Remove all position classes
                item.classList.remove('active', 'prev-2', 'prev-1', 'next-1', 'next-2', 'hidden');

                const position = index - currentIndex;

                if (position === 0) {
                    item.classList.add('active');
                } else if (position === -1) {
                    item.classList.add('prev-1');
                } else if (position === -2) {
                    item.classList.add('prev-2');
                } else if (position === 1) {
                    item.classList.add('next-1');
                } else if (position === 2) {
                    item.classList.add('next-2');
                } else {
                    item.classList.add('hidden');
                }
            });

            // Update indicators
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
        }

        // Open pricing modal
        function openPricingModal(imageName) {
            const modal = document.getElementById('pricing-modal');
            const modalImg = document.getElementById('pricing-modal-img');
            
            modalImg.src = `images/cenik/${imageName}`;
            modal.classList.add('active');
        }

        // Close pricing modal
        function closePricingModal() {
            const modal = document.getElementById('pricing-modal');
            modal.classList.remove('active');
        }

        // Initialize pricing modal functionality
        function initializePricingModal() {
            const modal = document.getElementById('pricing-modal');
            const closeBtn = document.getElementById('pricing-modal-close');
            const prevBtn = document.getElementById('pricing-prev');
            const nextBtn = document.getElementById('pricing-next');
            const currentSpan = document.getElementById('pricing-current');
            const totalSpan = document.getElementById('pricing-total');

            let currentPricingIndex = 0;

            // Update counter display
            function updatePricingCounter() {
                if (currentSpan && totalSpan) {
                    currentSpan.textContent = currentPricingIndex + 1;
                    totalSpan.textContent = coverflowImages.length;
                }
            }

            // Show specific pricing image
            function showPricingImage(index) {
                if (index >= 0 && index < coverflowImages.length) {
                    currentPricingIndex = index;
                    const modalImg = document.getElementById('pricing-modal-img');
                    modalImg.src = `images/cenik/${coverflowImages[index]}`;
                    updatePricingCounter();
                }
            }

            // Navigate to previous image
            function showPrevPricingImage() {
                const newIndex = currentPricingIndex > 0 ? currentPricingIndex - 1 : coverflowImages.length - 1;
                showPricingImage(newIndex);
            }

            // Navigate to next image
            function showNextPricingImage() {
                const newIndex = currentPricingIndex < coverflowImages.length - 1 ? currentPricingIndex + 1 : 0;
                showPricingImage(newIndex);
            }

            // Close modal when clicking close button
            closeBtn.addEventListener('click', closePricingModal);

            // Navigation buttons
            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showPrevPricingImage();
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showNextPricingImage();
                });
            }

            // Close modal when clicking outside the image
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closePricingModal();
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (modal.classList.contains('active')) {
                    switch(e.key) {
                        case 'Escape':
                            closePricingModal();
                            break;
                        case 'ArrowLeft':
                            showPrevPricingImage();
                            break;
                        case 'ArrowRight':
                            showNextPricingImage();
                            break;
                    }
                }
            });

            // Update global openPricingModal function to set current index
            window.openPricingModal = function(imageName) {
                const index = coverflowImages.indexOf(imageName);
                if (index !== -1) {
                    currentPricingIndex = index;
                }
                const modal = document.getElementById('pricing-modal');
                const modalImg = document.getElementById('pricing-modal-img');

                modalImg.src = `images/cenik/${imageName}`;
                modal.classList.add('active');
                updatePricingCounter();
            };
        }

        // Load pricing images on page load
        loadPricingImages();
        initializePricingModal();

        // Reservation Form Handler
        const reservationForm = document.getElementById('reservation-form');
        const reservationMessage = document.getElementById('reservation-message');
        
        if (reservationForm) {
            reservationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = reservationForm.querySelector('.reservation-btn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                // Show loading state
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
                reservationMessage.style.display = 'none';
                
                // Collect form data
                const formData = new FormData(reservationForm);
                
                // Submit form
                fetch('submit_reservation.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loading state
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                    
                    // Show message
                    reservationMessage.style.display = 'block';
                    reservationMessage.className = 'reservation-message ' + (data.success ? 'success' : 'error');
                    reservationMessage.textContent = data.message;
                    
                    // Reset form on success
                    if (data.success) {
                        reservationForm.reset();
                        // Scroll to message
                        reservationMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Hide loading state
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                    
                    // Show error message
                    reservationMessage.style.display = 'block';
                    reservationMessage.className = 'reservation-message error';
                    reservationMessage.textContent = 'Došlo k chybě při odesílání formuláře. Zkuste to prosím znovu.';
                });
            });
        }
    });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.current-year').forEach(function(el) {
        el.textContent = new Date().getFullYear();
    });
});
</script>

<!-- Pop-up Announcement Modal -->
<div id="site-popup-modal" class="site-popup-overlay" style="display: none;">
    <div class="site-popup-card">
        <button type="button" class="site-popup-close" onclick="closeSitePopup()">&times;</button>
        <div id="site-popup-image-container" style="display: none;">
            <img id="site-popup-image" src="" alt="Oznámení">
        </div>
        <div class="site-popup-body">
            <h2 id="site-popup-title"></h2>
            <p id="site-popup-text"></p>
            <div class="site-popup-footer">
                <button type="button" class="btn primary" onclick="closeSitePopup()">Zavřít</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
