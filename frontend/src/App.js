import React from 'react';
import { Route, Routes, Navigate } from 'react-router-dom';
import Container from 'react-bootstrap/Container';
import NavBar from './components/NavBar';
import ProductList from './components/ProductList';
import ProductDetail from './components/ProductDetail';
import CategoryList from './components/CategoryList';
import Cart from './components/Cart';
import Checkout from './components/Checkout';
import Login from './components/Login';
import Register from './components/Register';
import PrivateRoute from './components/PrivateRoute';

// admin pages
import AdminProductList from './components/AdminProductList';
import ProductForm from './components/ProductForm';
import AdminCategoryList from './components/AdminCategoryList';
import CategoryForm from './components/CategoryForm';

function App() {
  return (
    <>
      <NavBar />
      <Container className="mt-4">
        <Routes>
          <Route path="/" element={<Navigate to="/products" />} />
          <Route path="/products" element={<ProductList />} />
          <Route path="/products/:id" element={<ProductDetail />} />
          <Route path="/categories" element={<CategoryList />} />
          <Route path="/cart" element={<PrivateRoute component={Cart} />} />
          <Route path="/checkout" element={<PrivateRoute component={Checkout} />} />
          {/* admin crud pages */}
          <Route path="/admin/products" element={<PrivateRoute component={AdminProductList} requireAdmin />} />
          <Route path="/admin/products/new" element={<PrivateRoute component={ProductForm} requireAdmin />} />
          <Route path="/admin/products/:id/edit" element={<PrivateRoute component={ProductForm} requireAdmin />} />
          <Route path="/admin/categories" element={<PrivateRoute component={AdminCategoryList} requireAdmin />} />
          <Route path="/admin/categories/new" element={<PrivateRoute component={CategoryForm} requireAdmin />} />
          <Route path="/admin/categories/:id/edit" element={<PrivateRoute component={CategoryForm} requireAdmin />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="*" element={<h2>Page not found</h2>} />
        </Routes>
      </Container>
    </>
  );
}

export default App;
