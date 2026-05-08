<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details | Firozabad Mart</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Header / Navbar -->
    <header class="navbar">
        <div class="container nav-container">
            <a href="index.html" class="logo">
                <span>Firozabad</span>Mart
            </a>
            <nav class="nav-links">
                <a href="index.html">Home</a>
                <a href="about-us.html">About Us</a>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle">Bangles <i class="fa-solid fa-chevron-down"></i></a>
                    <div class="dropdown-menu glass">
                        <a href="#">Glass Bangles</a>
                        <a href="#">Name Bangles</a>
                        <a href="#">Wedding Bangles</a>
                        <a href="#">Fancy Stone Bangles</a>
                        <a href="#">Glass Kade</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="chandeliers-lights.html" class="dropdown-toggle">Fancy Light <i class="fa-solid fa-chevron-down"></i></a>
                    <div class="dropdown-menu glass">
                        <a href="chandeliers-lights.html">Chandeliers</a>
                        <a href="#">Corner Lights</a>
                        <a href="#">Hanging Lights</a>
                        <a href="#">Table Lamps</a>
                        <a href="#">Wall Lights</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle">Glasses <i class="fa-solid fa-chevron-down"></i></a>
                    <div class="dropdown-menu glass">
                        <a href="#">Whisky Glasses</a>
                        <a href="#">Wine Glasses</a>
                        <a href="#">Beer Mugs</a>
                        <a href="#">Water Glasses</a>
                    </div>
                </div>
                <a href="contact-us.html" class="btn btn-primary">Contact Us</a>
            </nav>
            <div class="mobile-menu-btn">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
    </header>

    <!-- Product Details Section -->
    <section class="product-details-page section-padding">
        <div class="container">
            <ul class="breadcrumb" style="margin-bottom: 2rem;">
                <li><a href="index.html">Home</a></li>
                <li><a href="chandeliers-lights.html">Chandeliers</a></li>
                <li id="breadcrumb-product-code" class="active">FMC 001</li>
            </ul>

            <div class="product-details-grid">
                <!-- Product Gallery -->
                <div class="product-gallery glass-card">
                    <img src="assets/images/chandelier_light.png" alt="Product Image" id="main-product-img">
                </div>

                <!-- Product Info -->
                <div class="product-info-detailed">
                    <h1 id="product-title">Premium Crystal Chandelier</h1>
                    <p class="product-sku">Product Code: <span id="product-code-val">FMC 001</span></p>
                    <div class="price-box">
                        <span class="price-label">Price:</span>
                        <span class="price-value text-gradient">Request Quote</span>
                    </div>
                    
                    <div class="product-description">
                        <h3>Product Description</h3>
                        <p>Accentuate your rooms with aesthetic appeal by adding this exclusive crystal chandelier. Expertly crafted in Firozabad, this piece offers timeless elegance and superior brilliance.</p>
                        <ul>
                            <li>Premium High-Quality Glass</li>
                            <li>Scratch-Resistant Finish</li>
                            <li>Durable Brass/Metal Fittings</li>
                            <li>Customizable Size & Design</li>
                        </ul>
                    </div>

                    <div class="product-actions">
                        <a href="https://api.whatsapp.com/send?phone=+91-8796065252&text=Hello, I am interested in Product FMC 001" class="btn btn-primary btn-large">
                            <i class="fa-brands fa-whatsapp"></i> INQUIRE VIA WHATSAPP
                        </a>
                        <a href="javascript:void(0)" class="btn btn-outline btn-large" id="open-quote-modal">GET EMAIL QUOTE</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quote Modal -->
    <div class="modal-overlay" id="quote-modal">
        <div class="modal-content glass-card">
            <div class="close-modal" id="close-modal">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <div class="modal-header">
                <h2>Get a Quote</h2>
                <p>Send an inquiry for <span id="modal-product-name" class="text-gradient" style="font-weight: 700;">FMC 001</span></p>
            </div>
            <form class="modal-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="modal-name">Full Name</label>
                        <input type="text" id="modal-name" placeholder="John Doe" class="glass-input" required>
                    </div>
                    <div class="form-group">
                        <label for="modal-email">Email</label>
                        <input type="email" id="modal-email" placeholder="john@example.com" class="glass-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="modal-phone">Phone Number</label>
                    <input type="text" id="modal-phone" placeholder="+91 00000 00000" class="glass-input">
                </div>
                <div class="form-group">
                    <label for="modal-message">Message</label>
                    <textarea id="modal-message" rows="4" class="glass-input" placeholder="I am interested in this product and would like to know the pricing..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-large w-100">SEND INQUIRY</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer glass">
        <div class="container footer-grid">
            <div class="footer-info">
                <a href="index.html" class="logo"><span>Firozabad</span>Mart</a>
                <p>Leading manufacturer and bulk supplier of premium glass products, serving retailers and commercial buyers across India.</p>
                <div class="social-links">
                    <a href="https://api.whatsapp.com/send?phone=+91-8796065252&text=Hello" target="_blank" class="glass-icon"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#" class="glass-icon"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="glass-icon"><i class="fa-brands fa-facebook-f"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h4>Categories</h4>
                <ul>
                    <li><a href="#">Glass Bangles</a></li>
                    <li><a href="chandeliers-lights.html">Chandeliers</a></li>
                    <li><a href="#">Whisky & Wine Glasses</a></li>
                    <li><a href="#">Hanging Lights</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about-us.html">About Us</a></li>
                    <li><a href="contact-us.html">Contact Us</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Get In Touch</h4>
                <div class="contact-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <p>266. Street No. 9, Premeshawar Gate, Firozabad (U.P.) India</p>
                </div>
                <div class="contact-item">
                    <i class="fa-solid fa-phone"></i>
                    <p>+91-879 606 5252<br>+91-9927496014</p>
                </div>
                <div class="contact-item">
                    <i class="fa-solid fa-envelope"></i>
                    <p>info@firozabadmart.com</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <span id="year"></span> Firozabad Mart. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();

        // Simple URL Parameter Handling for Template
        const urlParams = new URLSearchParams(window.location.search);
        const code = urlParams.get('code');
        if (code) {
            const formattedCode = code.replace(/([A-Z]+)(\d+)/, '$1 $2');
            document.getElementById('product-code-val').textContent = formattedCode;
            document.getElementById('breadcrumb-product-code').textContent = formattedCode;
            document.getElementById('product-title').textContent = "Product " + code;
            document.getElementById('modal-product-name').textContent = formattedCode;
        }

        // Modal Logic
        const modal = document.getElementById('quote-modal');
        const openBtn = document.getElementById('open-quote-modal');
        const closeBtn = document.getElementById('close-modal');

        openBtn.addEventListener('click', () => {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        });

        const closeModal = () => {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        };

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Form Submission (Demo)
        const form = document.querySelector('.modal-form');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = form.querySelector('button');
            const originalText = btn.textContent;
            
            btn.textContent = 'SENDING...';
            btn.disabled = true;

            setTimeout(() => {
                btn.textContent = 'INQUIRY SENT!';
                btn.style.background = '#4CAF50';
                
                setTimeout(() => {
                    closeModal();
                    // Reset button
                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.style.background = '';
                        btn.disabled = false;
                        form.reset();
                    }, 500);
                }, 1500);
            }, 1000);
        });
    </script>
</body>
</html>
