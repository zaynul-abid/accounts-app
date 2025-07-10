@extends('backend.layouts.app')
@section('title','Dashboard')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header','Dashboard')
@section('sub-header','Dashboard')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($totalIncome, 2) }}</h3>
                            <p>Total Incomes</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ route('incomes.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($totalExpense, 2) }}</h3>
                            <p>Total Expenses</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{route('expenses.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{number_format($balance)}}</h3>
                            <p>Balance</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route('income-expense-summary') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{$suppliers}}</h3>
                            <p>Suppliers</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="{{route('suppliers.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>

            <!-- /.row -->
            <!-- Main row -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Income vs Expense Ratio</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="ratioChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Financial Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-wallet"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Income</span>
                                    <span class="info-box-number">{{ number_format($totalIncome, 2) }}</span>
                                </div>
                            </div>
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-receipt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Expense</span>
                                    <span class="info-box-number">{{ number_format($totalExpense, 2) }}</span>
                                </div>
                            </div>
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-balance-scale"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Current Balance</span>
                                    <span class="info-box-number">{{ number_format($balance, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row (main row) -->
        </div><!-- /.container-fluid -->
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('ratioChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Income', 'Expense'],
                    datasets: [{
                        data: [{{ $totalIncome }}, {{ $totalExpense }}],
                        backgroundColor: ['#17a2b8', '#28a745']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endsection
