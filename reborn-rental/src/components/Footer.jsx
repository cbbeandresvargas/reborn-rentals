export default function Footer() {
return (
<footer className="site-footer">
<div className="footer-left">
<div className="chat-bubbles">
<div className="bubble" />
<div className="bubble" />
</div>
<div className="legal">
© 2025 <strong>Reborn Rentals</strong>, All Rights Reserved.
<div className="links">
<a href="#">terms & conditions</a>
<span>•</span>
<a href="#">privacy policy</a>
</div>
</div>
</div>
<div className="footer-right">
<div className="social">
<div className="icon" title="Facebook" />
<div className="icon" title="Instagram" />
<div className="icon" title="LinkedIn" />
</div>
<div className="payments">
<span className="pm visa">VISA</span>
<span className="pm mc">MC</span>
<span className="pm ap">Pay</span>
</div>
</div>
</footer>
)
}