export default function CartButton({ count = 0, onClick }) {
return (
<button className="cart-btn" onClick={onClick} aria-label="Open cart">
{/* icono carrito */}
<svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
<path d="M6 6h15l-1.5 9h-12zM6 6l-2-2M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm9 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
</svg>
{count > 0 && <span className="badge">{count}</span>}
</button>
)
}