// Landing Page JavaScript

(function($) {
    'use strict';

    // Side Navigation
    $('#side-nav-open').on('click', function(e) {
        e.preventDefault();
        $('#side-nav').css('width', '300px');
        $('#canvas-overlay').fadeIn();
    });

    $('#side-nav-close, #canvas-overlay').on('click', function() {
        $('#side-nav').css('width', '0');
        $('#canvas-overlay').fadeOut();
    });

    // Smooth Scroll
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });

    // Navbar scroll effect
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 50) {
            $('#navbar-header').addClass('scrolled').css({
                'box-shadow': '0 5px 20px rgba(0,0,0,0.15)',
                'padding': '10px 0'
            });
        } else {
            $('#navbar-header').removeClass('scrolled').css({
                'box-shadow': '0 2px 10px rgba(0,0,0,0.1)',
                'padding': '20px 0'
            });
        }
    });

    // Mobile menu close on link click
    $('.navbar-nav>li>a').on('click', function() {
        $('.navbar-collapse').collapse('hide');
    });

    // Form validation
    $('form[name="contact-us"]').on('submit', function(e) {
        e.preventDefault();
        
        var name = $('#name').val();
        var email = $('#email').val();
        var phone = $('#phoneNumber').val();
        
        if (!name || !email || !phone) {
            alert('Por favor completa todos los campos obligatorios');
            return false;
        }
        
        // Email validation
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert('Por favor ingresa un email válido');
            return false;
        }
        
        alert('¡Gracias por tu interés! Nos pondremos en contacto contigo pronto.');
        this.reset();
    });

})(jQuery);
