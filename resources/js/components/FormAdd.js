/**
 * FormAdd.js
 *
 * @file For a creation of a form
 *
 * @version 0.1.0 2019-10-17 MH
 * @author Michael Han <mhan1@unm.edu>
 */
import React,{ useRef, useState } from "react";
import FormText from './FormText';
import FormNumber from './FormNumber';
import FormDropdown from './FormDropdown';

export default function FormAdd({ formAttributes }) {

  return (
    <>
      <form method="POST" action={_saveformCallback}>
      <input type="hidden" name="_token" value={_csrf_token}/>
      {formAttributes.fieldData.map( (field,i) => {
        switch (field.type) {
          case 'text':
            return <FormText key={i} field={field} />;
            break;
          case 'number':
            return <FormNumber key={i} field={field} />;
            break;
          case 'dropdown':
            return <FormDropdown key={i} field={field} />;
            break;
          default:
            console.error('Unknown form field type');
        }
      })}
      <div className="row">
        <div className="col pb-3">
          <button type="submit" className="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
    </>
  );
};
