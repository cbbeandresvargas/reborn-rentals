import { useMemo, useState } from 'react'
import { PRODUCTS } from '../data/products'
import Header from '../components/Header'
import Breadcrumb from '../components/Breadcrumb'
import ProductCard from '../components/ProductCard'
import CartDrawer from '../components/CartDrawer'
import Footer from '../components/Footer'
import '../styles/home.css'


export default function Home() {
const [cartOpen, setCartOpen] = useState(false)
const [cartItems, setCartItems] = useState([]) // {id,title,pricePerDay,qty,code,img}


const addToCart = (p) => {
setCartItems((prev) => {
const idx = prev.findIndex((x) => x.id === p.id)
if (idx >= 0) {
const copy = [...prev]
copy[idx] = { ...copy[idx], qty: copy[idx].qty + 1 }
return copy
}
return [...prev, { ...p, qty: 1 }]
})
}


const inc = (id) => setCartItems((prev) => prev.map(it => it.id === id ? { ...it, qty: it.qty + 1 } : it))
const dec = (id) => setCartItems((prev) => prev.flatMap(it => it.id !== id ? [it] : (it.qty > 1 ? [{ ...it, qty: it.qty - 1 }] : [])))
const remove = (id) => setCartItems((prev) => prev.filter(it => it.id !== id))


const count = useMemo(() => cartItems.reduce((acc, it) => acc + it.qty, 0), [cartItems])


return (
<div className="home">
{/* HEADER */}
<Header cartCount={count} onOpenCart={() => setCartOpen(true)} />


{/* BREADCRUMB */}
<Breadcrumb items={[{ label: 'Home', href: '#' }, { label: 'Washout Pans', current: true }]} />

{/* TITLE / SUBTITLE */}
<section className="page-head">
<h1>Washout Pans <span className="muted">({PRODUCTS.length})</span></h1>
<p className="lead">Concrete washout pans and lids for rent, used in contamination prevention.</p>
<p className="hint">DRAG AND DROP ITEMS INTO CART</p>
</section>


{/* GRID */}
<section className="grid">
{PRODUCTS.map(p => (
<ProductCard key={p.id} p={p} onAdd={addToCart} />
))}
</section>


{/* FOOTER */}
<Footer />


{/* DRAWER CARRITO */}
<CartDrawer
open={cartOpen}
items={cartItems}
onClose={() => setCartOpen(false)}
onInc={inc}
onDec={dec}
onRemove={remove}
/>
</div>
)
}