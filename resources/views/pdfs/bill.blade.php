<!-- Format Number -->
<?php 

  // $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

  // TO get the first letters
  preg_match_all('/\b\w/', $bill->company->name, $matches); 

?>

<!DOCTYPE html>

<html lang="en">

  <head>

    <title>OSVL/17-18/{{ $bill->invoice_no }}</title>

    <!-- Style-->
    <style type="text/css">
      
      body {
        font-family: "Times New Roman", Georgia, Serif;  
        padding-top: 70px;
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

  </head>

  <body> 

    <!-- Header -->
    <!-- <div class="header">
      <img src="" width="100%">
    </div> -->  

    <!-- Bill Heading -->
    <h3 align="center">TAX INVOICE</h3>

    <!-- Basic Details -->
    <div class="wrapper">

      <div class="div-inline">

        <table>

          <!-- Name -->
          <tr>
            <td>
              <b>Name</b>
            </td>
            <td colspan="2">
              <b>{{ $bill->company->name }}</b>
            </td>
          </tr>

          <!-- Address -->
          <tr>
            <td>
              <b>Address</b>
            </td>
            <td colspan="2">
              {{ $bill->company->address }}
            </td>
          </tr>

          <!-- State Code -->
          <tr> 
            <td>
              <b>State Code : </b>
            </td>
            <td colspan="2">
              {{ $bill->company->state_code }}
            </td>
          </tr>

          <!-- GSTN No -->
          <tr>
            <td>
              <b>GSTN No.</b>
            </td>
            <td colspan="2">
              {{ $bill->company->gstn_no }}
            </td>
          </tr> 

        </table> 
        
      </div>

      <div class="div-inline">

        <table>

          <!-- Invoice No -->
          <tr>
            <td>
              <b>Invoice No</b>
            </td>
            <td colspan="2">
              <b>{{ implode('', $matches[0]) }}/17-18/{{ $bill->bill_no }}</b>
            </td>
          </tr>

          <!-- Invoice Date -->
          <tr>
            <td>
              <b>Invoice Date</b>
            </td>
            <td colspan="2">
              {{ Carbon\Carbon::parse($bill->created_at)->format('d-m-Y') }}  
            </td>
          </tr>

          <!-- Dispatch Document No -->
          <tr>
            <td>
              <b>Dispatch Document No.</b>
            </td>
            <td colspan="2">
              {{ $bill->equip_details }}
            </td>
          </tr>

          <!-- Dispatched Through -->
          <tr>
            <td>
              <b>Dispatched Through</b>
            </td>
            <td colspan="2">
              {{ $bill->reg_no }}
            </td>
          </tr> 

        </table>
        
      </div>

    </div>  

    <!-- Consignee Details --> 
    <div class="wrapper">

      <div class="div-inline">

        <table>

          <!-- Name -->
          <tr>
            <td>
              <b>Consignee Name</b>
            </td>
            <td colspan="2">
              <b>{{ $bill->company->name }}</b>
            </td>
          </tr>

          <!-- Address -->
          <tr>
            <td>
              <b>Address</b>
            </td>
            <td colspan="2">
              {{ $bill->company->address }}
            </td>
          </tr>

          <!-- State Code -->
          <tr> 
            <td>
              <b>State Code : </b>
            </td>
            <td colspan="2">
              {{ $bill->company->state_code }}
            </td>
          </tr>

          <!-- GSTN No -->
          <tr>
            <td>
              <b>GSTN No.</b>
            </td>
            <td colspan="2">
              {{ $bill->company->gstn_no }}
            </td>
          </tr> 

        </table> 
        
      </div>

      <div class="div-inline">

        <table>

          <!-- Delivery Note -->
          <tr>
            <td>
              <b>Delivery Note</b>
            </td>
            <td colspan="2">
              {{ $bill->log_sheet_no }}
            </td>
          </tr>

          <!-- Delivery Note Date -->
          <tr>
            <td>
              <b>Delivery Note Date</b>
            </td>
            <td colspan="2"> 
            </td>
          </tr>

          <!-- Mode/Terms of payment -->
          <tr>
            <td>
              <b>Mode/Terms of payment</b>
            </td>
            <td colspan="2"> 
            </td>
          </tr>

        </table>
        
      </div>

    </div> 

    <!-- Charges Details -->
    <h4><u>Bill Description as follows:</u></h4>
    <div class="wrapper">

      <table>
        <!-- Heading   -->
        <tr align="center">
          <td>
            <b>Sr. No.</b>
          </td>
          <td colspan="3">
            <b>Description of Goods</b>
          </td>
          <td>
            <b>Quantity</b>
          </td> 
          <td>
            <b>Amount</b>
          </td>
        </tr>

        <!-- Billing details -->
        @foreach($bill->billing_details as $billing_detail)
        <tr align="center" class="noBottomBorder"> 

            <!-- Sr. No -->
            <td style="height:40px;" class="align-top">
              {{ $loop->index + 1 }}
            </td>

            <!-- Description -->
            <td colspan="3">
              {{ $billing_detail->product_category->name }}
            </td>

            <!-- Quantity -->
            <td>
              {{ $billing_detail->qty }}
            </td>

            <!-- Amount -->
            <td>
              Rs. {{ number_format($billing_detail->amount) }}
            </td>

        </tr>
        @endforeach  

        <!-- Sub total -->
        <tr style="border-top-color: black !important">
          
          <td>
          </td>
          <td colspan="3">
          </td> 

          <!-- Amount -->
          <td>
            <b>Amount</b>
          </td>
          <td align="center">
            {{ number_format( $bill->sub_total ) }}
          </td>

        </tr>

        <!-- GSTN Part -->
        <tr>
          
          <!-- GSTN No -->
          <td>
            <b>GSTN No.</b>
          </td>
          <td colspan="3">
            {{ $bill->company->gstn_no }}
          </td>

          <!-- SGST -->
          @foreach($bill->billing_taxes as $billing_tax)
          <td>
            <b>SGST Tax @ {{ $billing_tax->tax->tax_percent/2 }}%</b>
          </td>
          <td align="center">
            @if($bill->customer->state_code == $bill->company->state_code)
              {{ number_format( $billing_tax->amount/2 ) }}
            @endif
          </td>
          @endforeach

        </tr>

        <!-- Pan Part -->
        <tr>
          
          <!-- PAN No -->
          <td>
            <b>PAN No.</b>
          </td>
          <td colspan="3">
            {{ $bill->company->pan_no }}
          </td>

          <!-- CGST -->
          @foreach($bill->billing_taxes as $billing_tax)
          <td>
            <b>CGST Tax @ {{ $billing_tax->tax->tax_percent/2 }}%</b>
          </td>
          <td align="center">
            @if($bill->customer->state_code == $bill->company->state_code)
              {{ number_format( $billing_tax->amount/2 ) }}
            @endif
          </td>
          @endforeach

        </tr>

        <!-- Amount in words Part -->
        <tr>
          
          <!-- Amount in words -->
          <td rowspan="2">
            <b>Amount in words</b>
          </td>
          <td colspan="3" rowspan="2"> 
          </td>

          <!-- IGST -->
          @foreach($bill->billing_taxes as $billing_tax)
          <td>
            <b>IGST Tax @ 18%</b>
          </td>
          <td align="center">
            @if($bill->customer->state_code != $bill->company->state_code)
              {{ number_format( $billing_tax->amount ) }}
            @endif
          </td>
          @endforeach

        </tr> 

        <tr>

          <!-- Grand Total -->
          <td>
            <b>Grand Total</b>
          </td>
          <td align="center">
            <?php $total_tax = 0; ?>
            @foreach($bill->billing_taxes as $billing_tax)
              <?php $total_tax += $billing_tax->amount ?>
            @endforeach
            <?php $total = $bill->sub_total + $total_tax ?>
            {{ number_format( $total ) }}
          </td>

        </tr>


      </table>
      
    </div> 

    <!-- Account Details  -->
    <div class="wrapper">

      <table>
          
        <tr align="center">
          <td>
            <b>Account Details</b>
          </td>
          <td>
            <b>Name :</b>
            <br>
            {{ $bill->company->acc_name }}
          </td>
          <td>
            <b>A/C No. :</b>
            <br>
            {{ $bill->company->acc_no }}
          </td>
          <td>
            <b>IFSC Code :</b>
            <br>
            {{ $bill->company->ifsc }}
          </td>
          <td>
            <b>Branch :</b>
            <br>
            {{ $bill->company->branch }}
          </td>
        </tr>

      </table>
      
    </div> 

    <!-- Terms -->
    <div class="wrapper">

      <table>

        <tr>
          
          <td>

            <b>Terms and Conditions </b> 
            <br>

            1. Interest @ 18% p.a. will be charged if this payment is not paid within 15 days after submission.
            <br>
            2. Error and submission in this invoice shall be subject to the jurisdication of Panvel

          </td>

          <td style="vertical-align: top" align="center">
            <small>
              Certified that the particular given above are true and correct
            </small>
            <br>
            <b>For {{ strtoupper($bill->company->name) }}</b>
            <br>
            <br> 
            <br> 
            <label>
              <b>Authorized Signatory</b>
            </label>
          </td>

        </tr>  

      </table>
      
    </div> 
  
  </body>

</html>