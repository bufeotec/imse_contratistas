<div>
    <div class="card">
        <div class="card-datatable table-responsive">
            <table {!! $attributes->merge(['class' => 'invoice-list-table table table-border-top-0  table-hover']) !!}>
                <thead class="table-dark">
                    {{ $thead }}
                </thead>
                <tbody class="table-border-bottom-0">
                    {{ $tbody }}
                </tbody>
            </table>
        </div>
    </div>
</div>
