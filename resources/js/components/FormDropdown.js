/**
 * FormDropdown.js
 *
 * @file Dropdown field component
 *
 * @version 0.1.0 2019-10-17 MH
 * @author Michael Han <mhan1@unm.edu>
 */
import React from "react";

export default function FormDropdown({field,value}) {
    return (
<div className="form-group">
  <label htmlFor={field.identifier}>{field.label}</label>
  <select className="form-control" name={field.identifier} id={field.identifier} defaultValue={value}>
    {field.values.map( (val,i) => {
      return (
        <option key={i} value={val}>{val}</option>
      );
    })}
  </select>
</div>);
}
