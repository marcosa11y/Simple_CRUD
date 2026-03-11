import React, { useState, useEffect } from 'react';
import api from '../api';
import { Table, Button } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import '../styles/Cart.css';

const Cart = () => {
    const [cart, setCart] = useState(null);
    const navigate = useNavigate();

    const loadCart = async () => {
        const res = await api.get('/carts');
        setCart(res.data);
    };

    useEffect(() => {
        loadCart();
    }, []);

    const removeItem = async (id) => {
        await api.delete(`/carts/items/${id}`);
        loadCart();
    };

    const handleCheckout = () => {
        navigate('/checkout');
    };

    if (!cart) return <div>Loading...</div>;

    return (
        <div>
            <h2>Your Cart</h2>
            {cart.items && cart.items.length > 0 ? (
                <>
                    <Table striped bordered hover>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {cart.items.map((item) => (
                                <tr key={item.id}>
                                    <td>{item.product ? item.product.name : 'Unknown'}</td>
                                    <td>{item.quantity}</td>
                                    <td>
                                        <Button
                                            variant="danger"
                                            size="sm"
                                            onClick={() => removeItem(item.id)}
                                        >
                                            Remove
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                    <Button onClick={handleCheckout}>Checkout</Button>
                </>
            ) : (
                <p>Your cart is empty.</p>
            )}
        </div>
    );
};
export default Cart;
