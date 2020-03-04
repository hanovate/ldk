/**
 * Add.js
 *
 * @file An add form render file for FormAdd
 *
 * @version 0.1.0 2019-10-17 MH
 * @author Michael Han <mhan1@unm.edu>
 */
import React from 'react';
import { render } from "react-dom";
import FormAdd from "./FormAdd";

/*
const _data_attributes = {
  form: [
    {
      identifier: 'hs_code',
      label: 'high school code',
      type: 'number',
      attributes: {
        min: 0,
        max: 1000000,
        required: 'required'
      }
    },
    {
      identifier: 'district_code',
      label: 'district code',
      type: 'number',
      attributes: {
        min: 0,
        max: 1000,
        required: 'required'
      }
    },
    {
      identifier: 'district_name',
      label: 'district name',
      type: 'text',
      attributes: {
        required: 'required'
      }
    },
    {
      identifier: 'public_or_not',
      label: 'public or not',
      type: 'dropdown',
      values: ['P','N']
    }
  ]
};
*/

render(
  <FormAdd formAttributes={_form_attributes} />
  , document.getElementById(_target_tag)
);
