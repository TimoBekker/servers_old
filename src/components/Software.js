import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { Typography } from 'antd';

const { Title } = Typography;

const SoftwareList = () => {
  return (
    <div>
      <Title level={2}>Программное обеспечение</Title>
      <p>Здесь будет список ПО</p>
    </div>
  );
};

const Software = () => {
  return (
    <Routes>
      <Route path="/distributions" element={<SoftwareList />} />
      <Route path="/installed" element={<div>Установленное ПО</div>} />
      <Route path="/" element={<SoftwareList />} />
    </Routes>
  );
};

export default Software;