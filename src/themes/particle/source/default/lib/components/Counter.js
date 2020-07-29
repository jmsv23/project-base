import React, { useState } from 'react';
import PropTypes from 'prop-types';

const Counter = (props) => {
  const [count, setCount] = useState(props.initCount);
  return (
    <>
      <h1>{count}</h1>
      <div className="flex">
        <button className="text-2xl p-2" onClick={() => setCount(count + 1)}>
          +
        </button>
        <button className="text-2xl p-2" onClick={() => setCount(count - 1)}>
          -
        </button>
      </div>
    </>
  );
};

Counter.defaultProps = {
  initCount: 0,
};

Counter.propTypes = {
  initCount: PropTypes.number,
};

export default Counter;
