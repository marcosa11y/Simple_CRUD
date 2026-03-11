import React, { useContext } from 'react';
import { Navigate } from 'react-router-dom';
import { AuthContext } from '../contexts/AuthContext';

const PrivateRoute = ({ component: Component, requireAdmin = false }) => {
    const { user } = useContext(AuthContext);
    if (!user) return <Navigate to="/login" />;
    if (requireAdmin && user.role !== 'admin') {
        // non-admins get bounced back to products
        return <Navigate to="/" />;
    }
    return <Component />;
}
export default PrivateRoute;
