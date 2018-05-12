
<!-- Style-->
<style type="text/css">
  
  body {
    font-family: "Times New Roman", Georgia, Serif;  
    //padding-top: 70px;
    font-size: 14px;
  } 

  .header {
    top: 0px;
    position: fixed;
  }

  .footer {
    bottom: 0px;
    position: fixed;
  }

  table {
    width: 100%;
    table-layout: fixed;
  }

  td {
    padding: 2px;
  }

  tr, td {
    border: 1px solid black;
  }

  table {
    border: 1px solid black;
    border-collapse: collapse;
  }

  tr.noBottomBorder td {
    border-bottom-color: transparent;
  }

  tr.noTopBorder td {
    border-top-color: transparent;
  } 

  .wrapper {
    width: 100%;
    padding-bottom: 3px;
  }

  .div-inline {
    display: inline-block; 
    vertical-align: top;
    width: 49.5%; 
  }

  .align-top {
    vertical-align: top;
  }

  .align-bottom {
    vertical-align: bottom;
  }

</style>

<!-- Bill Heading -->
<h3 align="center">Bills against {{ $customer->name }}</h3>

<table>  
  <tr align="center">
    <td><b>Date</b></td>
    <td><b>Bill No</b></td>
    <td><b>Bill Details</b></td> 
    <td><b>Bill Amount</b></td>
    <td><b>Other Details</b></td>
  </tr>
  @foreach($billings as $bill)
  <tr align="center">
    <td>
      {{ $bill->created_at->format('d-m-Y') }}
    </td>
    <td>
      {{ $settings->bill_format }}{{ $bill->bill_no }}
    </td>
    <td>
      @foreach($bill->billing_details as $bill_details)
        <b>{{ $bill_details->product_category->name }} (QTY: {{ $bill_details->qty }}) (Rs. {{ $bill_details->amount }})</b>
        <br>
      @endforeach
    </td>
    <td>
      Rs. {{ number_format( $bill->total_amount ) }}
    </td>
    <td>
      Dispatch Through: <b>{{ $bill->despatch_through }}</b>
    </td>
  </tr>
  @endforeach
</table>
