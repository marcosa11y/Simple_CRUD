import React, { useState } from 'react';
import api from '../api';
import { Button, Alert } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';

const Checkout = () => {
    const [message, setMessage] = useState('');
    const navigate = useNavigate();

    const handleCheckout = async () => {
        try {
            const res = await api.post('/orders/checkout');
            setMessage(res.data.message || 'Order placed');
            navigate('/');
        } catch (err) {
            setMessage(err.response?.data?.message || 'Error during checkout');
        }
    };

    return (
        <div>
            <h2>Checkout</h2>
            {message && <Alert variant="info">{message}</Alert>}
            <Button onClick={handleCheckout}>Confirm Order</Button>
        </div>
    );
};

export default Checkout;