export default function Breadcrumb({ items = [] }) {
return (
<nav className="breadcrumb" aria-label="Breadcrumb">
{items.map((it, idx) => (
<span key={idx} className={it.current ? 'current' : ''}>
{it.href ? <a href={it.href}>{it.label}</a> : it.label}
{idx < items.length - 1 && <span> / </span>}
</span>
))}
</nav>
)}