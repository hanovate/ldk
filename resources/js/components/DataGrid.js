/**
 * DataGrid.js
 *
 * @file A listing view with query and pagination features
 *
 * @version 0.1.0 2019-10-17 MH
 * @author Michael Han <mhan1@unm.edu>
 */
import React,{ useState } from "react";
import { FaCog, FaSortAlphaDown,FaSortAlphaUp,FaFilter,FaPlus } from "react-icons/fa";
import DataTable from './DataTable';
import Pagination from 'react-js-pagination';

const ItemsPerPage = ({size, onModify = f => f}) => (
  <>
    <div className="form-group">
      <select className="form-control" id="items-per-page" value={size} onChange={onModify}>
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="30">30</option>
        <option value="50">50</option>
        <option value="75">75</option>
        <option value="100">100</option>
      </select>
      <label htmlFor="items-per-page">Items per Page</label>
    </div>
  </>);


const NumberOfItems = ({total}) => (
    <div className="form-group">
        <p>Total of {total} records</p>
    </div>);


const Searchbar = ({ query, onChangeHandler = f => f }) => (
  <div className="form-group">
    <input type="searchterm" className="form-control" id="searchterm" aria-describedby="searchtermHelp" placeholder="Enter a keyword" value={query} onChange={onChangeHandler} />
  </div>);


export default function DataGrid({ tableAttributes })
{
  const [ query,setQuery ] = useState('');
  const [ limit,setLimit ] = useState(5);
  const [ offset,setOffset ] = useState(0);
  const [ total,setTotal ] = useState(0);
  const [ page, setPage ] = useState(1);

  const createformLink = _basePath + '/createform';

  const handlePageChange = (pg) => {
    setPage(pg);
    setOffset(Math.ceil((pg-1)*limit));
  };

  return (
    <>
    <div className="row">
      <div className="col-11 text-right pb-3">
        <a href={createformLink}><h4> Add a new record</h4></a>
      </div>
      <div className="col-1 text-right">
        <a href={createformLink}><FaPlus size="2em" /></a>
      </div>
    </div>
    <div className="row">
      <div className="col text-left">
        <div className="form-group">
          <input
            type="searchterm"
            className="form-control"
            id="searchterm"
            aria-describedby="searchtermHelp"
            placeholder="Search here"
            value={query}
            onChange={e => setQuery(e.target.value) }
          />
        </div>
      </div>
    </div>
    <div className="row">
      <div className="col">
          <DataTable tableAttributes={tableAttributes} query={query} limit={limit} offset={offset} updateTotal={(t)=>setTotal(t)}/>
      </div>
    </div>
    <div className="row">
      <div className="col-md-2 text-left"><NumberOfItems total={total}/></div>
      <div className="col-md-8 text-center">
        <nav aria-label="page navigation listing">
          <Pagination innerClass="pagination justify-content-center" itemClass="page-item" linkClass="page-link" activePage={page} itemsCountPerPage={limit} totalItemsCount={total} PageRangeDisplayed={7} hideFirstLastPages={false} onChange={(pg) => handlePageChange(pg)} />
        </nav>
      </div>
      <div className="col-md-2 text-right"><ItemsPerPage size={limit} onModify={(e) => setLimit(e.target.value)} /></div>
    </div>
    </>
  );
}
