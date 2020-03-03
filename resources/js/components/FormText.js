/**
 * FormText.js
 *
 * @file Text field component
 *
 * @version 0.1.0 2019-10-17 MH
 * @author Michael Han <mhan1@unm.edu>
 */
import React from "react";

export default function FormText({field,value}) {
    return (
<div className="form-group">
  <label htmlFor={field.identifier}>{field.label}</label>
  <input type={field.type} className="form-control" name={field.identifier} id={field.identifier} {...field.attributes} placeholder={field.placeholder===null ? '':field.placeholder} defaultValue={value} />
</div>);
}
