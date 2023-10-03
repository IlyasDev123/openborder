
<table id="google_analytics" class="table table-bordered table-hover">
	<thead>
		<tr>
            <th>#</th>
            <th>Date</th>
            <th>Visitors</th>
            <th>Page Title</th>
            <th>Page Views</th>
		</tr>
	</thead>
	<tbody>
      @forelse ($page_views_records as $data)
	  <tr>
		<td>
			{{$loop->index + 1 }}
		</td>
        <td>{{ $data['date'] }}</td>
        <td>{{ $data['visitors'] }}</td>
        <td>{{ $data['pageTitle'] }}</td>
        <td>{{ $data['pageViews'] }}</td>
	</tr>
	  @empty
		  <tr>No Record Found</tr>
	  @endforelse
		
	</tbody>
</table>
