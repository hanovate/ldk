/**
 * FormEdit.js
 *
 * @file An edit form generator
 *
 * @version 0.1.0 2019-10-18 MH
 * @author Michael Han <mhan1@unm.edu>
 */
import React,{ useRef, useState, useCallback } from "react";
import { useAsyncCombineSeq, useAsyncRun, useAsyncTaskDelay, useAsyncTaskFetch } from "react-hooks-async";
import FormText from './FormText';
import FormNumber from './FormNumber';
import FormDropdown from './FormDropdown';

const initparam = {
  headers: {
    Credentials: 'include',
    Authorization: _oauth_token_type + ' ' + _oauth_access_token,
    'user-agent': 'UNMReactJSRuntime/0.1.0',
    Accept: 'application/json',
  }
};

export default function FormEdit({ formAttributes })
{
  const apiurl = _apiurl + '/' + (_record_id || null);

  const delayTask = useAsyncTaskDelay(useCallback(() => 200, [_record_id]));
  const fetchTask = useAsyncTaskFetch(apiurl,initparam);
  const combinedTask = useAsyncCombineSeq(delayTask, fetchTask);

  useAsyncRun(combinedTask);

  if (delayTask.pending) return <div className="alert alert-info" role="alert">Waiting...</div>;
  if (fetchTask.error) return <div className="alert alert-danger" role="alert">Error found: {fetchTask.error.name} - {fetchTask.error.message}</div>;
  if (fetchTask.pending) return <div className="alert alert-primary" role="alert">Loading... <button onClick={fetchTask.abort}>Stop</button></div>;

  return (
    <>
      <form method="POST" action={_saveformCallback}>
      <input type="hidden" name="_token" value={_csrf_token}/>
      {formAttributes.fieldData.map( (field,i) => {
        switch (field.type) {
          case 'text':
            return <FormText key={i} field={field} value={eval('fetchTask.result.data.'+field.identifier)} />;
            break;
          case 'number':
            return <FormNumber key={i} field={field} value={eval('fetchTask.result.data.'+field.identifier)} />;
            break;
          case 'dropdown':
            return <FormDropdown key={i} field={field} value={eval('fetchTask.result.data.'+field.identifier)} />;
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
