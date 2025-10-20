import { useMemo } from 'react'


export default function CartDrawer({ open, items, onClose, onInc, onDec, onRemove }) {
const total = useMemo(() => items.reduce((acc, it) => acc + it.pricePerDay * it.qty, 0), [items])


return (
<>
<div className={`cart-drawer ${open ? 'open' : ''}`} role="dialog" aria-modal="true" aria-labelledby="cart-title">
<div className="cart-head">
<h3 id="cart-title">My Cart</h3>
<button className="icon-btn" onClick={onClose} aria-label="Close cart">✕</button>
</div>


<div className="cart-body">
{items.length === 0 ? (
<p className="muted-text">Your cart is empty.</p>
) : (
items.map((it) => (
<div key={it.id} className="cart-row">
<div className="cart-thumb">
<img
src={it.img}
alt={it.title}
onError={(e)=>{e.currentTarget.src='https://via.placeholder.com/60x40?text=IMG'}}
/>
</div>
<div className="cart-info">
<div className="cart-title">{it.title}</div>
<div className="cart-sku">ID: {it.code}</div>
<div className="cart-price">${it.pricePerDay}/day</div>
</div>
<div className="cart-qty">
<button className="qty-btn" onClick={() => onDec(it.id)}>-</button>
<span className="qty-num">{it.qty}</span>
<button className="qty-btn" onClick={() => onInc(it.id)}>+</button>
</div>
<button className="remove-btn" onClick={() => onRemove(it.id)} aria-label="Remove">Remove</button>
</div>
))
)}
</div>


<div className="cart-foot">
<div className="cart-total">
<span>Total/day</span>
<strong>${total.toFixed(2)}</strong>
</div>
<button className="btn btn-checkout" disabled={items.length === 0}>Checkout</button>
</div>
</div>
{/* Backdrop */}
{open && <div className="cart-backdrop" onClick={onClose} />}
</>
)
}