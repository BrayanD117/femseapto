import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import * as agreementsData from '../../../../../../assets/agreements/agreements.json';

@Component({
  selector: 'app-agreement-types-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './agreement-types-list.component.html',
  styleUrl: './agreement-types-list.component.css'
})
export class AgreementTypesListComponent implements OnInit {
  cards: { imageUrl: string; title: string; description: string }[] = [];
  title!: string;

  constructor(private route: ActivatedRoute) {}

  ngOnInit() {
    this.route.params.subscribe(params => {
      const type = params['type'];
      const data = (agreementsData as any)[type];

      if (data) {
        this.title = data.title;
        this.cards = data.cards;
      } else {
        this.title = 'No disponible';
        this.cards = [];
      }
    });
  }
}
