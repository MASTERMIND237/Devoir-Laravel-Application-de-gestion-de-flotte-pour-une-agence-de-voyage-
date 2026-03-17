@extends('app.master')

@section('body')
    <!-- Corps principal -->
    <main>
        <h2>Liste des Personnes</h2>
        <p>Voici un tableau présentant les informations de quelques personnes :</p>

        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Âge</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($table as $t)
                <tr>
                    <td>{{ $t['Nom'] }}</td>
                    <td>{{ $t['Age'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
@endsection
