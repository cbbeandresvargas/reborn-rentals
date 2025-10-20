export default function ProductCard({ p, onAdd }) {
return (
<article className="card" draggable>
<div className="price-badge">
<strong>{p.pricePerDay}$</strong>
<span>/day*</span>
</div>


<div className="card-media">
<img src={p.img} alt={p.title} onError={(e)=>{e.currentTarget.src='https://via.placeholder.com/240x130?text=Washout'}} />
</div>


<div className="card-body">
<p className="sku">ID: {p.code}</p>
<h3 className="card-title">{p.title}</h3>
{p.subtitle && <p className="card-sub">{p.subtitle}</p>}
</div>


<div className="card-actions">
<button className="btn btn-specs" onClick={() => onAdd(p)}>Add to cart</button>
</div>
</article>
)
}