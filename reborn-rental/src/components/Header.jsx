import CartButton from './CartButton'


export default function Header({ cartCount, onOpenCart }) {
return (
<header className="site-header">
<div className="brand">
<div className="brand-mark">
<span className="brand-bar" />
<span className="brand-bar" />
<span className="brand-bar" />
</div>
<span className="brand-text">
<strong>REBORN</strong> RENTAL
</span>
</div>


<CartButton count={cartCount} onClick={onOpenCart} />
</header>
)
}