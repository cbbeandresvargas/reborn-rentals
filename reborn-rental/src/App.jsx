// src/App.jsx
import { Outlet } from 'react-router-dom'

export default function App() {
  return (
    <div>
      <Outlet /> {/* aquí se monta Home en el CLIENTE */}
    </div>
  )
}
